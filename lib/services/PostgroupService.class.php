<?php
/**
 * blog_PostgroupService
 * @package blog
 */
class blog_PostgroupService extends f_persistentdocument_DocumentService
{
	/**
	 * @var blog_PostgroupService
	 */
	private static $instance;

	/**
	 * @return blog_PostgroupService
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
	 * @return blog_persistentdocument_postgroup
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_blog/postgroup');
	}

	/**
	 * Create a query based on 'modules_blog/postgroup' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_blog/postgroup');
	}

	/**
	 * @param blog_persistentdocument_postgroup $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
	protected function preInsert($document, $parentNodeId = null)
	{
		if ($parentNodeId !== null && $document->getBlog() === null)
		{
			$blog = blog_BlogService::getInstance()->getByParentNodeId($parentNodeId);
			if ($blog !== null)
			{
				$document->setBlog($blog);
			}
		}
	}
	
	/**
	 * @deprecated 
	 * @param blog_persistentdocument_postgroup $postgroup
	 */
	public function incrementPublishedPostCount($postgroup)
	{
		$this->updatePostCount($postgroup);
	}

	/**
	 * @deprecated 
	 * @param blog_persistentdocument_postgroup $postgroup
	 */
	public function decrementPublishedPostCount($postgroup)
	{
		$this->updatePostCount($postgroup);
	}

	/**
	 * @deprecated 
	 * @param blog_persistentdocument_postgroup $postgroup
	 * @param Integer $newCount
	 */
	protected function changePublishedPostCount($postgroup, $newCount)
	{
		$this->updatePostCount($postgroup);
	}
	
	/**
	 * @param blog_persistentdocument_postgroup $postgroup
	 * @return boolean;
	 */
	public function updatePostCount($postgroup)
	{
		try
		{
			$this->tm->beginTransaction();
			$modified = $this->calculatePostCount($postgroup);
			if ($modified)
			{
				$this->pp->updateDocument($postgroup);
				$this->publishDocumentIfPossible($postgroup);
			}
			$this->tm->commit();
		}
		catch (Exception $e)
		{
			$this->tm->rollBack($e);
			throw $e;
		}
		return $modified;
	}
	
	/**
	 * @param blog_persistentdocument_postgroup $postgroup
	 * @return boolean
	 */
	protected function calculatePostCount($postgroup)
	{
		$postgroup->setPublishedPostCount(0);
		return $postgroup->isPropertyModified('publishedPostCount');
	}

	/**
	 * @param blog_persistentdocument_postgroup $document
	 * @return Boolean
	 */
	public function isPublishable($document)
	{
		if ($document->getPublishedPostCount() <= 0)
		{
			return false;
		}
		return parent::isPublishable($document);
	}
	
	
	/**
	 * @see f_persistentdocument_DocumentService::getDisplayPage()
	 * @param f_persistentdocument_PersistentDocument $document
	 * @return website_persistentdocument_page
	 */
	public function getDisplayPage($document)
	{
		if ($document->isPublished())
		{
			$blog = $document->getBlog();
			$tag = 'functional_blog_' . $document->getPersistentModel()->getDocumentName(). '-detail';
			$page = TagService::getInstance()->getDocumentBySiblingTag($tag, $blog, false);
			return $page;
		}
		return parent::getDisplayPage($document);
	}
	
	/**
	 * @see f_persistentdocument_DocumentService::getResume()
	 *
	 * @param blog_persistentdocument_postgroup $document
	 * @param string $forModuleName
	 * @param array $allowedSections
	 * @return array
	 */
	public function getResume($document, $forModuleName, $allowedSections = null)
	{
		$data = parent::getResume($document, $forModuleName, $allowedSections);
		$data['properties']['publishedpostcount'] = $document->getPublishedPostCount();
		return $data;
	}

}