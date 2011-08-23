<?php
/**
 * blog_KeywordfolderService
 * @package blog
 */
class blog_KeywordfolderService extends generic_FolderService
{
	/**
	 * @var blog_KeywordfolderService
	 */
	private static $instance;

	/**
	 * @return blog_KeywordfolderService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @return blog_persistentdocument_keywordfolder
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_blog/keywordfolder');
	}

	/**
	 * Create a query based on 'modules_blog/keywordfolder' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_blog/keywordfolder');
	}
	
	/**
	 * Create a query based on 'modules_blog/keywordfolder' model.
	 * Only documents that are strictly instance of modules_blog/keywordfolder
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_blog/keywordfolder', false);
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 * @return blog_persistentdocument_keywordfolder
	 */
	public function getByBlog($blog)
	{
		return $this->createStrictQuery()->add(Restrictions::childOf($blog->getId()))->findUnique();
	}

	/**
	 * @param blog_persistentdocument_keywordfolder $document
	 * @param string[] $subModelNames
	 * @param integer $locateDocumentId null if use startindex
	 * @param integer $pageSize
	 * @param integer $startIndex
	 * @param integer $totalCount
	 * @return f_persistentdocument_PersistentDocument[]
	 */
	public function getVirtualChildrenAt($document, $subModelNames, $locateDocumentId, $pageSize, &$startIndex, &$totalCount)
	{
		$blogId = $this->getParentOf($document)->getId();

		$countQuery = blog_KeywordService::getInstance()->createQuery()->add(Restrictions::eq('blog.id', $blogId));	
		$countQuery->setProjection(Projections::rowCount('countItems'));
      	$resultCount = $countQuery->find();
		$totalCount = intval($resultCount[0]['countItems']);
				
		$query =  blog_KeywordService::getInstance()->createQuery()->add(Restrictions::eq('blog.id', $blogId));	
		$query->setFirstResult($startIndex)->setMaxResults($pageSize);
		return $query->find();
	}	
}