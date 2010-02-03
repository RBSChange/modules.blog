<?php
/**
 * blog_MonthService
 * @package blog
 */
class blog_MonthService extends blog_PostgroupService
{
	/**
	 * @var blog_MonthService
	 */
	private static $instance;

	/**
	 * @return blog_MonthService
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
	 * @return blog_persistentdocument_month
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_blog/month');
	}

	/**
	 * Create a query based on 'modules_blog/month' model.
	 * Return document that are instance of modules_blog/month,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_blog/month');
	}
	
	/**
	 * Create a query based on 'modules_blog/month' model.
	 * Only documents that are strictly instance of modules_blog/month
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_blog/month', false);
	}
	
	/**
	 * @param date_Calendar $date
	 * @param blog_persistentdocument_blog $blog
	 */
	public function getByDateAndBlog($date, $blog)
	{
		$month = null;
		if ($date !== null)
		{
			$year = blog_YearService::getInstance()->getByDateAndBlog($date, $blog);
			$month = $this->createQuery()
				->add(Restrictions::eq('number', $date->getMonth()))
				->add(Restrictions::eq('year.id', $year->getId()))
				->findUnique();
			if ($month === null)
			{
				$month = $this->getNewDocumentInstance();
				$month->setBlog($blog);
				$month->setYear($year);
				$month->setNumber($date->getMonth());
				$month->save();
			}
		}
		return $month;
	}
	
	/**
	 * @param blog_persistentdocument_month $month
	 * @param Integer $excludeId
	 */
	public function deleteIfUnused($month, $excludeId)
	{
		$query = blog_PostService::getInstance()->createQuery();
		$query->add(Restrictions::eq('month.id', $month->getId()));
		$query->add(Restrictions::ne('id', $excludeId));
		$query->setProjection(Projections::rowCount('total'));
		$row = $query->findUnique();
		Framework::debug(__METHOD__ . ' ' . var_export($row, true));
		Framework::debug(__METHOD__ . ' ' . var_export($row['total'], true));
		if ($row['total'] == '0')
		{
			Framework::debug(__METHOD__ . ' delete');
			$month->delete();
		}
	}
	
	/**
	 * @param blog_persistentdocument_year $year
	 * @return blog_persistentdocument_month[]
	 */
	public function getPublishedByYear($year)
	{
		return $this->createQuery()->add(Restrictions::eq('year.id', $year->getId()))->find();
	}
	
	/**
	 * @param blog_persistentdocument_month $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId = null)
	{
		// Set the label.
		$dateCalendar = date_Calendar::getInstanceFromFormat($document->getYear()->getNumber().'/'.$document->getNumber().'/1', 'Y/m/d');
		$document->setLabel(date_DateFormat::format($dateCalendar, 'F'));
	}


	/**
	 * @param blog_persistentdocument_month $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
	protected function preInsert($document, $parentNodeId = null)
	{
		// Overload the default postgroup method.
	}

	/**
	 * @param blog_persistentdocument_month $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postInsert($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param blog_persistentdocument_month $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preUpdate($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param blog_persistentdocument_month $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postUpdate($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param blog_persistentdocument_month $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postSave($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param blog_persistentdocument_month $document
	 * @return void
	 */
	protected function preDelete($document)
	{
		$document->setMeta('year', $document->getYear());
	}

	/**
	 * @param blog_persistentdocument_month $document
	 * @return void
	 */
//	protected function preDeleteLocalized($document)
//	{
//	}

	/**
	 * @param blog_persistentdocument_month $document
	 * @return void
	 */
	protected function postDelete($document)
	{
		$year = $document->getMeta('year');
		if ($year->getMonthCountInverse() == 0)
		{
			$year->delete();
		}
	}

	/**
	 * @param blog_persistentdocument_month $document
	 * @return void
	 */
//	protected function postDeleteLocalized($document)
//	{
//	}

	/**
	 * @param blog_persistentdocument_month $document
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
	 * @param blog_persistentdocument_month $document
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
	 * @param blog_persistentdocument_month $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagAdded($document, $tag)
//	{
//	}

	/**
	 * @param blog_persistentdocument_month $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagRemoved($document, $tag)
//	{
//	}

	/**
	 * @param blog_persistentdocument_month $fromDocument
	 * @param f_persistentdocument_PersistentDocument $toDocument
	 * @param String $tag
	 * @return void
	 */
//	public function tagMovedFrom($fromDocument, $toDocument, $tag)
//	{
//	}

	/**
	 * @param f_persistentdocument_PersistentDocument $fromDocument
	 * @param blog_persistentdocument_month $toDocument
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
	 * @param blog_persistentdocument_month $document
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
	 * @param blog_persistentdocument_month $document
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
	
	/**
	 * @param blog_persistentdocument_month $month
	 */
	public function incrementPublishedPostCount($month)
	{
		parent::incrementPublishedPostCount($month);
		
		// Update year counter.
		$year = $month->getYear();
		$year->getDocumentService()->incrementPublishedPostCount($year);
	}

	/**
	 * @param blog_persistentdocument_month $month
	 */
	public function decrementPublishedPostCount($month)
	{
		parent::decrementPublishedPostCount($month);
		
		// Update year counter.
		$year = $month->getYear();
		$year->getDocumentService()->decrementPublishedPostCount($year);
	}
}