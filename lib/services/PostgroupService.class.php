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
		$document->setBlog(blog_BlogService::getInstance()->getByParentNodeId($parentNodeId));
	}
	
	/**
	 * @param blog_persistentdocument_postgroup $postgroup
	 */
	public function incrementPublishedPostCount($postgroup)
	{
		$this->changePublishedPostCount($postgroup, $postgroup->getPublishedPostCount()+1);
	}

	/**
	 * @param blog_persistentdocument_postgroup $postgroup
	 */
	public function decrementPublishedPostCount($postgroup)
	{
		$this->changePublishedPostCount($postgroup, $postgroup->getPublishedPostCount()-1);
	}

	/**
	 * @param blog_persistentdocument_postgroup $postgroup
	 * @param Integer $newCount
	 */
	protected function changePublishedPostCount($postgroup, $newCount)
	{
		$oldCount = $postgroup->getPublishedPostCount();
		if ($oldCount != $newCount)
		{
			try
			{
				$this->tm->beginTransaction();
				$postgroup->setPublishedPostCount($newCount);
				$this->pp->updateDocument($postgroup);
				if ($newCount == 0 || $oldCount == 0)
				{
					$this->publishDocumentIfPossible($postgroup);
				}
				$this->tm->commit();
			}
			catch (Exception $e)
			{
				$this->tm->rollBack($e);
			}
		}
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
			$page = TagService::getInstance()->getDocumentBySiblingTag('functional_blog_' . $document->getPersistentModel()->getDocumentName(). '-detail', $blog);
			return $page;
		}
		return parent::getDisplayPage($document);
	}
}