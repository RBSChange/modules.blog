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
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
//	protected function preSave($document, $parentNodeId = null)
//	{
//
//	}

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
	 * @param blog_persistentdocument_postgroup $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postInsert($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param blog_persistentdocument_postgroup $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preUpdate($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param blog_persistentdocument_postgroup $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postUpdate($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param blog_persistentdocument_postgroup $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postSave($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param blog_persistentdocument_postgroup $document
	 * @return void
	 */
//	protected function preDelete($document)
//	{
//	}

	/**
	 * @param blog_persistentdocument_postgroup $document
	 * @return void
	 */
//	protected function preDeleteLocalized($document)
//	{
//	}

	/**
	 * @param blog_persistentdocument_postgroup $document
	 * @return void
	 */
//	protected function postDelete($document)
//	{
//	}

	/**
	 * @param blog_persistentdocument_postgroup $document
	 * @return void
	 */
//	protected function postDeleteLocalized($document)
//	{
//	}

	/**
	 * @param blog_persistentdocument_postgroup $document
	 * @return boolean true if the document is publishable, false if it is not.
	 */
//	public function isPublishable($document)
//	{
//		$result = parent::isPublishable($document);
//		return $result;
//	}


	/**
	 * Methode à surcharger pour effectuer des post traitement apres le changement de status du document
	 * utiliser $document->getPublicationstatus() pour retrouver le nouveau status du document.
	 * @param blog_persistentdocument_postgroup $document
	 * @param String $oldPublicationStatus
	 * @param array<"cause" => String, "modifiedPropertyNames" => array, "oldPropertyValues" => array> $params
	 * @return void
	 */
//	protected function publicationStatusChanged($document, $oldPublicationStatus, $params)
//	{
//	}

	/**
	 * Correction document is available via $args['correction'].
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param Array<String=>mixed> $args
	 */
//	protected function onCorrectionActivated($document, $args)
//	{
//	}

	/**
	 * @param blog_persistentdocument_postgroup $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagAdded($document, $tag)
//	{
//	}

	/**
	 * @param blog_persistentdocument_postgroup $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagRemoved($document, $tag)
//	{
//	}

	/**
	 * @param blog_persistentdocument_postgroup $fromDocument
	 * @param f_persistentdocument_PersistentDocument $toDocument
	 * @param String $tag
	 * @return void
	 */
//	public function tagMovedFrom($fromDocument, $toDocument, $tag)
//	{
//	}

	/**
	 * @param f_persistentdocument_PersistentDocument $fromDocument
	 * @param blog_persistentdocument_postgroup $toDocument
	 * @param String $tag
	 * @return void
	 */
//	public function tagMovedTo($fromDocument, $toDocument, $tag)
//	{
//	}

	/**
	 * Called before the moveToOperation starts. The method is executed INSIDE a
	 * transaction.
	 *
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param Integer $destId
	 */
//	protected function onMoveToStart($document, $destId)
//	{
//	}

	/**
	 * @param blog_persistentdocument_postgroup $document
	 * @param Integer $destId
	 * @return void
	 */
//	protected function onDocumentMoved($document, $destId)
//	{
//	}

	/**
	 * this method is call before save the duplicate document.
	 * If this method not override in the document service, the document isn't duplicable.
	 * An IllegalOperationException is so launched.
	 *
	 * @param f_persistentdocument_PersistentDocument $newDocument
	 * @param f_persistentdocument_PersistentDocument $originalDocument
	 * @param Integer $parentNodeId
	 *
	 * @throws IllegalOperationException
	 */
//	protected function preDuplicate($newDocument, $originalDocument, $parentNodeId)
//	{
//		throw new IllegalOperationException('This document cannot be duplicated.');
//	}

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
}