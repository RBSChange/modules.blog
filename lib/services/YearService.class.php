<?php
/**
 * blog_YearService
 * @package blog
 */
class blog_YearService extends blog_PostgroupService
{
	/**
	 * @var blog_YearService
	 */
	private static $instance;

	/**
	 * @return blog_YearService
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
	 * @return blog_persistentdocument_year
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_blog/year');
	}

	/**
	 * Create a query based on 'modules_blog/year' model.
	 * Return document that are instance of modules_blog/year,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_blog/year');
	}
	
	/**
	 * Create a query based on 'modules_blog/year' model.
	 * Only documents that are strictly instance of modules_blog/year
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_blog/year', false);
	}
	
	/**
	 * @param date_Calendar $date
	 * @param blog_persistentdocument_blog $blog
	 * @return blog_persistentdocument_year
	 */
	public function getByDateAndBlog($date, $blog)
	{
		$year = null;
		if ($date !== null)
		{
			$year = $this->createQuery()
				->add(Restrictions::eq('number', $date->getYear()))
				->add(Restrictions::eq('blog.id', $blog->getId()))
				->findUnique();
			if ($year === null)
			{
				$year = $this->getNewDocumentInstance();
				$year->setBlog($blog);
				$year->setNumber($date->getYear());
				$year->save();
			}
		}
		return $year;
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 * @return blog_persistentdocument_year[]
	 */
	public function getPublishedByBlog($blog)
	{
		return $this->createQuery()->add(Restrictions::eq('blog.id', $blog->getId()))->find();
	}
	
	/**
	 * @param blog_persistentdocument_year $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId = null)
	{
		// Copy number in label.
		$document->setLabel($document->getNumber());
	}


	/**
	 * @param blog_persistentdocument_year $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
	protected function preInsert($document, $parentNodeId = null)
	{
		// Overload the default postgroup method.
	}

	/**
	 * @param blog_persistentdocument_year $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postInsert($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param blog_persistentdocument_year $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preUpdate($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param blog_persistentdocument_year $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postUpdate($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param blog_persistentdocument_year $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postSave($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param blog_persistentdocument_year $document
	 * @return void
	 */
//	protected function preDelete($document)
//	{
//	}

	/**
	 * @param blog_persistentdocument_year $document
	 * @return void
	 */
//	protected function preDeleteLocalized($document)
//	{
//	}

	/**
	 * @param blog_persistentdocument_year $document
	 * @return void
	 */
//	protected function postDelete($document)
//	{
//	}

	/**
	 * @param blog_persistentdocument_year $document
	 * @return void
	 */
//	protected function postDeleteLocalized($document)
//	{
//	}

	/**
	 * @param blog_persistentdocument_year $document
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
	 * @param blog_persistentdocument_year $document
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
	 * @param blog_persistentdocument_year $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagAdded($document, $tag)
//	{
//	}

	/**
	 * @param blog_persistentdocument_year $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagRemoved($document, $tag)
//	{
//	}

	/**
	 * @param blog_persistentdocument_year $fromDocument
	 * @param f_persistentdocument_PersistentDocument $toDocument
	 * @param String $tag
	 * @return void
	 */
//	public function tagMovedFrom($fromDocument, $toDocument, $tag)
//	{
//	}

	/**
	 * @param f_persistentdocument_PersistentDocument $fromDocument
	 * @param blog_persistentdocument_year $toDocument
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
	 * @param blog_persistentdocument_year $document
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
	 * @param blog_persistentdocument_year $document
	 * @return Boolean
	 */
	public function isPublishable($document)
	{
		// A post keyword is publishable only if there is at least one related published post.
		if ($document->getPublishedPostCount() <= 0)
		{
			return false;
		}
		return parent::isPublishable($document);
	}
}