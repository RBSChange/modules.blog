<?php
/**
 * blog_CategoryService
 * @package blog
 */
class blog_CategoryService extends blog_PostgroupService
{
	/**
	 * @var blog_CategoryService
	 */
	private static $instance;

	/**
	 * @return blog_CategoryService
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
	 * @return blog_persistentdocument_category
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_blog/category');
	}

	/**
	 * Create a query based on 'modules_blog/category' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_blog/category');
	}
	
	/**
	 * Create a query based on 'modules_blog/category' model.
	 * Only documents that are strictly instance of modules_blog/category
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_blog/category', false);
	}
	
	/**
	 * @param blog_persistentdocument_category $document
	 * @param string[] $subModelNames
	 * @param integer $locateDocumentId null if use startindex
	 * @param integer $pageSize
	 * @param integer $startIndex
	 * @param integer $totalCount
	 * @return f_persistentdocument_PersistentDocument[]
	 */
	public function getVirtualChildrenAt($document, $subModelNames, $locateDocumentId, $pageSize, &$startIndex, &$totalCount)
	{
		$countQuery = blog_PostService::getInstance()->createQuery()->add(Restrictions::eq('category', $document));
		if (f_persistentdocument_PersistentDocumentModel::getInstance("blog", "post")->useCorrection())
		{
			$countQuery->add(Restrictions::isNull('correctionofid'));
		}		
		$countQuery->setProjection(Projections::rowCount('countItems'));
      	$resultCount = $countQuery->find();
		$totalCount = intval($resultCount[0]['countItems']);
			
			
		$query = blog_PostService::getInstance()->createQuery()
				->add(Restrictions::eq('category', $document));
		if (f_persistentdocument_PersistentDocumentModel::getInstance("blog", "post")->useCorrection())
		{
			$query->add(Restrictions::isNull('correctionofid'));
		}
		$query->addOrder(Order::desc('postdate'));
		$query->setFirstResult($startIndex)->setMaxResults($pageSize);
		return $query->find();
	}	
	
	/**
	 * @param blog_persistentdocument_category $category
	 * @param Boolean $includeDescendants if set to false, only posts related directly to the given 
	 * 		category are returned. Else, posts related to descendent categories of the 
	 * 		given one are returned too.
	 */
	public function getSortedPosts($category, $includeDescendants = true)
	{
		$query = blog_PostService::getInstance()->createQuery();
		$query->add(Restrictions::published());
		if ($includeDescendants)
		{
			$query->createCriteria('category')->add(Restrictions::orExp(
				Restrictions::eq('id', $category->getId()),
				Restrictions::descendentOf($category->getId())
			));			
		}
		else 
		{
			$query->add(Restrictions::eq('category.id', $category->getId()));
		}
		$query->addOrder(Order::desc('postdate'));
		return $query->find();
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 * @return blog_persistentdocument_category[]
	 */
	public function getPublishedFistLevelByBlog($blog)
	{
		$cfs = blog_CategoryfolderService::getInstance();
		$categoryFolder = $cfs->getByBlog($blog);
		return $this->createQuery()->add(Restrictions::published())
			->add(Restrictions::childOf($categoryFolder->getId()))->find();
	}
	
	/**
	 * @param blog_persistentdocument_category $category
	 * @return blog_persistentdocument_category[]
	 */
	public function getPublishedSubCategories($category)
	{
		return $this->createQuery()->add(Restrictions::published())
			->add(Restrictions::childOf($category->getId()))->find();
	}
	
	/**
	 * @param blog_persistentdocument_category $category
	 * @return blog_persistentdocument_category[]
	 */
	public function getPublishedAncestorCategories($category)
	{
		return $this->createQuery()->add(Restrictions::published())
			->add(Restrictions::ancestorOf($category->getId()))->find();
	}
		
	/**
	 * @param blog_persistentdocument_category $keyword
	 * @return rss_FeedWriter
	 */
	public function getRSSFeedWriter($category)
	{
		$restriction = Restrictions::eq('category.id', $category->getId());
		return blog_PostService::getInstance()->getRSSFeedWriterByRestriction($restriction);
	}

	private $ancestorIds = array();

	/**
	 * @param blog_persistentdocument_category $document
	 * @return void
	 */
	protected function preDelete($document)
	{
		if (count($document->getPublishedPostCount()))
		{
			$id = $document->getId();
			$ancestorIds = $this->createQuery()->add(Restrictions::ancestorOf($id))
				->setProjection(Projections::groupProperty('id', 'id'))->findColumn('id');
				
			if (count($ancestorIds))
			{
				$this->ancestorIds[$id] = $ancestorIds;
			}
		}
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @return void
	 */
	protected function postDelete($document)
	{
		$id = $document->getId();
		if (isset($this->ancestorIds[$id]))
		{
			try
			{
				$this->tm->beginTransaction();
				foreach ($this->ancestorIds[$id] as $categoryId) 
				{
					$category = DocumentHelper::getDocumentInstance($categoryId, 'modules_blog/category');
					if ($this->calculatePostCount($category, false))
					{
						$this->pp->updateDocument($category);
						$this->publishDocumentIfPossible($category);
					}
				}
				$this->tm->commit();
			}
			catch (Exception $e)
			{
				$this->tm->rollBack($e);
				throw $e;
			}
			unset($this->ancestorIds[$id]);			
		}
	}
		
	/**
	 * Called before the moveToOperation starts. The method is executed INSIDE a
	 * transaction.
	 *
	 * @param blog_persistentdocument_category $document
	 * @param Integer $destId
	 */
	protected function onMoveToStart($document, $destId)
	{
		
		$id = $document->getId();
		$categoryIds = array();
		
		// Refresh old ancestors counters.
		$categoryIds = $this->createQuery()
			->add(Restrictions::ancestorOf($id))
			->setProjection(Projections::groupProperty('id', 'id'))->findColumn('id');

	
		// Refresh new ancestors counters.
		$newParent = DocumentHelper::getDocumentInstance($destId);
		if ($newParent instanceof blog_persistentcument_category)
		{
			$categoryIds[] = $newParent->getId();

			$categoryIds = array_merge($categoryIds, $this->createQuery()
				->add(Restrictions::ancestorOf($destId))
				->setProjection(Projections::groupProperty('id', 'id'))->findColumn('id'));
		}
		
		$this->ancestorIds[$id] = array_unique($categoryIds);
	}
	
	/**
	 * Called upon successful moveTo operation. The method is executed OUTSIDE a
	 * transaction.
	 *
	 * @param blog_persistentdocument_category $document
	 * @param Integer $destId
	 */
	protected function onDocumentMoved($document, $destId)
	{
		$id = $document->getId();
		if (isset($this->ancestorIds[$id]))
		{
			try
			{
				$this->tm->beginTransaction();
				foreach ($this->ancestorIds[$id] as $categoryId) 
				{
					$category = DocumentHelper::getDocumentInstance($categoryId, 'modules_blog/category');
					if ($this->calculatePostCount($category, false))
					{
						$this->pp->updateDocument($category);
						$this->publishDocumentIfPossible($category);
					}
				}
				$this->tm->commit();
			}
			catch (Exception $e)
			{
				$this->tm->rollBack($e);
				throw $e;
			}
			unset($this->ancestorIds[$id]);
		}
	}	

	/**
	 * @param blog_persistentdocument_category $document
	 * @return Boolean
	 */
	public function isPublishable($document)
	{
		// A post category is publishable only if there is at least one published post
		// related the it or on of its descendants.
		if ($document->getRecursivePublishedPostCount() + $document->getPublishedPostCount() <= 0)
		{
			return false;
		}
		return true;
	}
	
	
	/**
	 * @see blog_PostgroupService::calculatePostCount()
	 *
	 * @param blog_persistentdocument_category $document
	 * @param boolean $updateAncestors 
	 * @return boolean
	 */
	protected function calculatePostCount($document, $updateAncestors = true)
	{
		$query = blog_PostService::getInstance()->createQuery()
			->add(Restrictions::published())
			->add(Restrictions::eq('category', $document))
			->setProjection(Projections::rowCount('count'));				
		if (f_persistentdocument_PersistentDocumentModel::getInstance("blog", "post")->useCorrection())
		{
			$query->add(Restrictions::isNull('correctionofid'));
		}	
		$result = $query->findUnique();
		$count = $result['count'];
		$document->setPublishedPostCount($count);
		
		$modified = $document->isPropertyModified('publishedPostCount');
		
		$categoryIds = $this->createQuery()
			->add(Restrictions::descendentOf($document->getId()))
			->setProjection(Projections::groupProperty('id', 'id'))->findColumn('id');
		
		if (count($categoryIds) > 0)
		{
			$query = blog_PostService::getInstance()->createQuery()
						->add(Restrictions::published())
						->add(Restrictions::in('category.id', $categoryIds))
						->setProjection(Projections::rowCount('count'));				
			if (f_persistentdocument_PersistentDocumentModel::getInstance("blog", "post")->useCorrection())
			{
				$query->add(Restrictions::isNull('correctionofid'));
			}	
			$result = $query->findUnique();			
			$count = $count + $result['count'];
		}

		$document->setRecursivePublishedPostCount($count);
		$modified = $modified || $document->isPropertyModified('recursivePublishedPostCount');

		if ($updateAncestors && $modified)
		{
			$ancestors = $this->createQuery()->add(Restrictions::ancestorOf($document->getId()))->find();
			foreach ($ancestors as $ancestor) 
			{
				if ($this->updatePostCount($ancestor, false))
				{
					$this->pp->updateDocument($ancestor);
					$this->publishDocumentIfPossible($ancestor);
				}
			}
		}
		return $modified;
	}
		
	/**
	 * @deprecated 
	 */
	public function incrementPublishedPostCount($category)
	{
		$this->updatePostCount($category);
	}

	/**
	 * @deprecated 
	 */
	public function decrementPublishedPostCount($category)
	{
		$this->updatePostCount($category);
	}
	
	/**
	 * @deprecated
	 */
	public function refreshPublishedPostCount($category)
	{
		$this->updatePostCount($category);
	}

	/**
	 * @deprecated
	 */
	private function changeRecursivePublishedPostCount($category, $newCount)
	{
		$this->updatePostCount($category);
	}
	
	/**
	 * @deprecated
	 */
	public function refreshRecursivePublishedPostCount($category)
	{
		$this->updatePostCount($category);
	}
	
	// Tweets handling.
	
	/**
	 * @param blog_persistentdocument_blog $document
	 * @return string[]
	 */
	public function getDocumentsModelNamesForTweet($document)
	{
		return array('modules_blog/post');
	}
}