<?php
/**
 * blog_KeywordService
 * @package blog
 */
class blog_KeywordService extends blog_PostgroupService
{
	/**
	 * @var blog_KeywordService
	 */
	private static $instance;

	/**
	 * @return blog_KeywordService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @return blog_persistentdocument_keyword
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_blog/keyword');
	}

	/**
	 * Create a query based on 'modules_blog/keyword' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_blog/keyword');
	}
	
	/**
	 * Create a query based on 'modules_blog/keyword' model.
	 * Only documents that are strictly instance of modules_blog/keyword
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_blog/keyword', false);
	}
	
	/**
	 * @param blog_persistentdocument_keyword $keyword
	 */
	public function getSortedPosts($keyword)
	{
		$query = blog_PostService::getInstance()->createQuery();
		$query->add(Restrictions::published());
		$query->add(Restrictions::eq('keyword.id', $keyword->getId()));
		$query->addOrder(Order::desc('postdate'));
		return $query->find();
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 * @param Interger $limit
	 */
	public function getMostUsedByBlog($blog, $limit)
	{
		$query = $this->createQuery();
		$query->add(Restrictions::published());
		$query->add(Restrictions::eq('blog.id', $blog->getId()));
		$query->addOrder(Order::desc('publishedpostcount'));
		$query->setMaxResults($limit);
		return $query->find();
	}
	
	/**
	 * @param blog_persistentdocument_keyword $keyword
	 * @return rss_FeedWriter
	 */
	public function getRSSFeedWriter($keyword)
	{
		$restriction = Restrictions::eq('keyword.id', $keyword->getId());
		return blog_PostService::getInstance()->getRSSFeedWriterByRestriction($restriction);
	}
	
	/**
	 * @param String $label
	 */
	private function buildComparablelabel($label)
	{
		return f_util_StringUtils::strtolower(website_UrlRewritingService::getInstance()->getUrlLabel($label));
	}
	
	/**
	 * @param blog_persistentdocument_keyword $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId = null)
	{
		parent::preSave($document, $parentNodeId);
		$document->setComparablelabel($this->buildComparablelabel($document->getLabel()));
	}

	/**
	 * @param blog_persistentdocument_keyword $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
	protected function preInsert($document, $parentNodeId = null)
	{
		parent::preInsert($document, $parentNodeId);	
		$document->setInsertInTree(false);
	}

	/**
	 * @param String $label
	 * @param blog_persistentdocument_blog $blog
	 * @return blog_persistentdocument_keyword
	 */
	public function getByLabelInBlog($label, $blog)
	{
		$query = $this->createStrictQuery();
		$query->add(Restrictions::eq('comparablelabel', $this->buildComparablelabel($label), true));
		$query->add(Restrictions::eq('blog.id', $blog->getId()));
		return $query->findUnique();
	}
		
	/**
	 * @see blog_PostgroupService::calculatePostCount()
	 *
	 * @param blog_persistentdocument_keyword $keyword
	 * @return boolean
	 */
	protected function calculatePostCount($keyword)
	{
		$query = blog_PostService::getInstance()->createQuery()
			->add(Restrictions::published())
			->add(Restrictions::eq('keyword', $keyword))
			->setProjection(Projections::rowCount('count'));	
		if (f_persistentdocument_PersistentDocumentModel::getInstance("blog", "post")->useCorrection())
		{
			$query->add(Restrictions::isNull('correctionofid'));
		}
				
		$result = $query->findUnique();	
		$keyword->setPublishedPostCount($result['count']);
		return $keyword->isPropertyModified('publishedPostCount') || $keyword->isPropertyModified('postCount');
	}
	
		
	/**
	 * @param website_persistentdoculent_website $website
	 * @param Integer $maxUrl
	 * @return Integer[]
	 */
	public function getIdsForSitemap($website, $maxUrl)
	{
		$query = $this->createQuery()->add(Restrictions::published());
		$query->createCriteria('blog')->add(Restrictions::descendentOf($website->getId()));
		return $query->setProjection(Projections::property('id'))->setMaxResults($maxUrl)->findColumn('id');	
	}

	/**
	 * @param blog_persistentdocument_keyword $keyword
	 * @param string $moduleName
	 * @param string $treeType
	 * @param array<string, string> $nodeAttributes
	 */
	public function addTreeAttributes($keyword, $moduleName, $treeType, &$nodeAttributes)
	{
		$query = blog_PostService::getInstance()->createQuery()
			->add(Restrictions::eq('keyword', $keyword))
			->setProjection(Projections::rowCount('count'));
				
		if (f_persistentdocument_PersistentDocumentModel::getInstance("blog", "post")->useCorrection())
		{
			$query->add(Restrictions::isNull('correctionofid'));
		}
		$result = $query->findUnique();			
		$nodeAttributes['postCount'] = $result['count'];
		$nodeAttributes['publishedPostCount'] = $keyword->getPublishedPostCount();
	}
	
	// Deprecated.
	
	/**
	 * @deprecated (will be removed in 4.0)
	 */
	public function refreshPublishedPostCount($keyword)
	{
		$this->updatePostCount($keyword);
	}
	
	/**
	 * @deprecated (will be removed in 4.0)
	 */
	public function refreshPostCount($keyword)
	{
		$this->updatePostCount($keyword);
	}	
	
	/**
	 * @deprecated (will be removed in 4.0)
	 */
	public function incrementPostCount($keyword)
	{
		$this->updatePostCount($keyword);
	}

	/**
	 * @deprecated (will be removed in 4.0)
	 */
	public function decrementPostCount($keyword)
	{
		$this->updatePostCount($keyword);
	}
}