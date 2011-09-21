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
			self::$instance = new self();
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
		$query = $this->createQuery()->add(Restrictions::eq('year.id', $year->getId()))->add(Restrictions::published());
		return $query->find();
	}
	
	/**
	 * @param blog_persistentdocument_month $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId)
	{
		// Set the label.
		$dateCalendar = date_Calendar::getInstanceFromFormat($document->getYear()->getNumber().'/'.$document->getNumber().'/1', 'Y/m/d');
		$document->setLabel(date_Formatter::format($dateCalendar, 'F'));
	}


	/**
	 * @param blog_persistentdocument_month $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
	protected function preInsert($document, $parentNodeId = null)
	{
		parent::preInsert($document, $parentNodeId);	
		$document->setInsertInTree(false);
	}
	
	private $checkYears = array();
	
	/**
	 * @param blog_persistentdocument_month $document
	 * @return void
	 */
	protected function preDelete($document)
	{
		$this->checkYears[$document->getId()] = $document->getYear();
	}

	/**
	 * @param blog_persistentdocument_month $document
	 * @return void
	 */
	protected function postDelete($document)
	{
		if (isset($this->checkYears[$document->getId()]))
		{
			blog_YearService::getInstance()->monthDeleted($this->checkYears[$document->getId()]);
			unset($this->checkYears[$document->getId()]);
		}		
	}

	/**
	 * @see blog_PostgroupService::calculatePostCount()
	 *
	 * @param blog_persistentdocument_month $document
	 * @return boolean
	 */
	protected function calculatePostCount($document)
	{
		$query = blog_PostService::getInstance()->createQuery()
			->add(Restrictions::published())
			->add(Restrictions::eq('month', $document))
			->setProjection(Projections::rowCount('count'));	
			
		if (f_persistentdocument_PersistentDocumentModel::getInstance("blog", "post")->useCorrection())
		{
			$query->add(Restrictions::isNull('correctionofid'));
		}		
		$result = $query->findUnique();
		$document->setPublishedPostCount($result['count']);
		$modified = $document->isPropertyModified('publishedPostCount');		
		if ($modified && $document->getYear())
		{
			blog_YearService::getInstance()->updatePostCount($document->getYear());
		}
		return $modified;
	}
}