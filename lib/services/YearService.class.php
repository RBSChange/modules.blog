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
		$query = $this->createQuery()->add(Restrictions::eq('blog.id', $blog->getId()))->add(Restrictions::published());
		return $query->find();
	}

	/**
	 * @param blog_persistentdocument_year $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
	protected function preInsert($document, $parentNodeId = null)
	{
		parent::preInsert($document, $parentNodeId);	
		$document->setInsertInTree(false);
	}
	
	/**
	 * @param blog_persistentdocument_year $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId)
	{
		// Copy number in label.
		$document->setLabel($document->getNumber());
	}

	/**
	 * @param blog_persistentdocument_year $document
	 * @return void
	 */	
	public function monthDeleted($document)
	{
		$result = $this->createQuery()
			->add(Restrictions::eq('id', $document->getId()))
			->add(Restrictions::isNotNull('month'))
			->setProjection(Projections::rowCount('count'))
			->findUnique();
			
		if ($result['count'] == 0)
		{
			$this->delete($document);	
		}
	}
	
	/**
	 * @see blog_PostgroupService::calculatePostCount()
	 *
	 * @param blog_persistentdocument_year $document
	 * @return boolean
	 */
	protected function calculatePostCount($document)
	{
		$query = blog_PostService::getInstance()->createQuery()
			->add(Restrictions::published())
			->add(Restrictions::eq('month.year', $document))
			->setProjection(Projections::rowCount('count'));				
		if (f_persistentdocument_PersistentDocumentModel::getInstance("blog", "post")->useCorrection())
		{
			$query->add(Restrictions::isNull('correctionofid'));
		}
		$result = $query->findUnique();	
		$document->setPublishedPostCount($result['count']);
		$modified = $document->isPropertyModified('publishedPostCount');		
		return $modified;
	}	
}