<?php
/**
 * blog_PostfolderService
 * @package blog
 */
class blog_PostfolderService extends generic_FolderService
{
	/**
	 * @var blog_PostfolderService
	 */
	private static $instance;

	/**
	 * @return blog_PostfolderService
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
	 * @return blog_persistentdocument_postfolder
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_blog/postfolder');
	}

	/**
	 * Create a query based on 'modules_blog/postfolder' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_blog/postfolder');
	}
	
	/**
	 * Create a query based on 'modules_blog/postfolder' model.
	 * Only documents that are strictly instance of modules_blog/postfolder
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_blog/postfolder', false);
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 * @return blog_persistentdocument_postfolder
	 */
	public function getByBlog($blog)
	{
		return $this->createStrictQuery()->add(Restrictions::childOf($blog->getId()))->findUnique();
	}
	
	/**
	 * @param blog_persistentdocument_postfolder $document
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

		$countQuery = blog_PostService::getInstance()->createQuery()->add(Restrictions::eq('blog.id', $blogId));
		if (f_persistentdocument_PersistentDocumentModel::getInstance("blog", "post")->useCorrection())
		{
			$countQuery->add(Restrictions::isNull('correctionofid'));
		}		
		$countQuery->setProjection(Projections::rowCount('countItems'));
      	$resultCount = $countQuery->find();
		$totalCount = intval($resultCount[0]['countItems']);
				
		$query = blog_PostService::getInstance()->createQuery()->add(Restrictions::eq('blog.id', $blogId));
		if (f_persistentdocument_PersistentDocumentModel::getInstance("blog", "post")->useCorrection())
		{
			$query->add(Restrictions::isNull('correctionofid'));
		}
		$query->addOrder(Order::desc('postdate'));
		$query->setFirstResult($startIndex)->setMaxResults($pageSize);
		return $query->find();
	}	
}