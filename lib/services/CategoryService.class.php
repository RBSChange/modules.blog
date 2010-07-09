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
		$categories = array();
		foreach ($cfs->getChildrenOf($categoryFolder, 'modules_blog/category') as $category)
		{
			if ($category->isPublished())
			{
				$categories[] = $category;
			}
		}
		return $categories;
	}
	
	/**
	 * @param blog_persistentdocument_category $category
	 * @return blog_persistentdocument_category[]
	 */
	public function getPublishedSubCategories($category)
	{
		$categories = array();
		foreach ($this->getChildrenOf($category, 'modules_blog/category') as $category)
		{
			if ($category->isPublished())
			{
				$categories[] = $category;
			}
		}
		return $categories;
	}
	
	/**
	 * @param blog_persistentdocument_category $category
	 * @return blog_persistentdocument_category[]
	 */
	public function getPublishedAncestorCategories($category)
	{
		$categories = array();
		foreach ($this->getAncestorsOf($category, 'modules_blog/category') as $category)
		{
			if ($category->isPublished())
			{
				$categories[] = $category;
			}
		}
		return $categories;
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
	
	/**
	 * @param blog_persistentdocument_category $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
//	protected function preSave($document, $parentNodeId = null)
//	{
//		parent::preSave($document, $parentNodeId);
//
//	}


	/**
	 * @param blog_persistentdocument_category $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preInsert($document, $parentNodeId = null)
//	{
//		parent::preInsert($document, $parentNodeId);
//	}

	/**
	 * @param blog_persistentdocument_category $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postInsert($document, $parentNodeId = null)
//	{
//		parent::postInsert($document, $parentNodeId);
//	}

	/**
	 * @param blog_persistentdocument_category $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preUpdate($document, $parentNodeId = null)
//	{
//		parent::preUpdate($document, $parentNodeId);
//	}

	/**
	 * @param blog_persistentdocument_category $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postUpdate($document, $parentNodeId = null)
//	{
//		parent::postUpdate($document, $parentNodeId);
//	}

	/**
	 * @param blog_persistentdocument_category $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postSave($document, $parentNodeId = null)
//	{
//		parent::postSave($document, $parentNodeId);
//	}

	/**
	 * @param blog_persistentdocument_category $document
	 * @return void
	 */
//	protected function preDelete($document)
//	{
//		parent::preDelete($document);
//	}

	/**
	 * @param blog_persistentdocument_category $document
	 * @return void
	 */
//	protected function preDeleteLocalized($document)
//	{
//		parent::preDeleteLocalized($document);
//	}

	/**
	 * @param blog_persistentdocument_category $document
	 * @return void
	 */
//	protected function postDelete($document)
//	{
//		parent::postDelete($document);
//	}

	/**
	 * @param blog_persistentdocument_category $document
	 * @return void
	 */
//	protected function postDeleteLocalized($document)
//	{
//		parent::postDeleteLocalized($document);
//	}

	/**
	 * @param blog_persistentdocument_category $document
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
	 * @param blog_persistentdocument_category $document
	 * @param String $oldPublicationStatus
	 * @param array<"cause" => String, "modifiedPropertyNames" => array, "oldPropertyValues" => array> $params
	 * @return void
	 */
//	protected function publicationStatusChanged($document, $oldPublicationStatus, $params)
//	{
//		parent::publicationStatusChanged($document, $oldPublicationStatus, $params);
//	}

	/**
	 * Correction document is available via $args['correction'].
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param Array<String=>mixed> $args
	 */
//	protected function onCorrectionActivated($document, $args)
//	{
//		parent::onCorrectionActivated($document, $args);
//	}

	/**
	 * @param blog_persistentdocument_category $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagAdded($document, $tag)
//	{
//		parent::tagAdded($document, $tag);
//	}

	/**
	 * @param blog_persistentdocument_category $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagRemoved($document, $tag)
//	{
//		parent::tagRemoved($document, $tag);
//	}

	/**
	 * @param blog_persistentdocument_category $fromDocument
	 * @param f_persistentdocument_PersistentDocument $toDocument
	 * @param String $tag
	 * @return void
	 */
//	public function tagMovedFrom($fromDocument, $toDocument, $tag)
//	{
//		parent::tagMovedFrom($fromDocument, $toDocument, $tag);
//	}

	/**
	 * @param f_persistentdocument_PersistentDocument $fromDocument
	 * @param blog_persistentdocument_category $toDocument
	 * @param String $tag
	 * @return void
	 */
//	public function tagMovedTo($fromDocument, $toDocument, $tag)
//	{
//		parent::tagMovedTo($fromDocument, $toDocument, $tag);
//	}

	/**
	 * Called before the moveToOperation starts. The method is executed INSIDE a
	 * transaction.
	 *
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param Integer $destId
	 */
	protected function onMoveToStart($document, $destId)
	{
		parent::onMoveToStart($document, $destId);
		
		// Refresh old ancestors counters.
		$ancestors = $this->getAncestorsOf($document, 'modules_blog/category');
		foreach ($ancestors as $ancestor)
		{
			$newCount = $ancestor->getRecursivePublishedDocumentCount() - $document->getRecursivePublishedDocumentCount();
			$this->changeRecursivePublishedPostCount($ancestor, $newCount);
		}
		
		// Refresh new ancestors counters.
		$newParent = DocumentHelper::getDocumentInstance($destId);
		if ($newParent instanceof blog_persistentcument_category)
		{
			$ancestors = $this->getAncestorsOf($newParent, 'modules_blog/category');
			$ancestors[] = $newParent;
			foreach ($ancestors as $ancestor)
			{
				$newCount = $ancestor->getRecursivePublishedDocumentCount() + $document->getRecursivePublishedDocumentCount();
				$this->changeRecursivePublishedPostCount($ancestor, $newCount);
			}
		}
	}

	/**
	 * @param blog_persistentdocument_category $document
	 * @param Integer $destId
	 * @return void
	 */
//	protected function onDocumentMoved($document, $destId)
//	{
//		parent::onDocumentMoved($document, $destId);
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
	 * @param blog_persistentdocument_category $document
	 * @return Boolean
	 */
	public function isPublishable($document)
	{
		// A post category is publishable only if there is at least one published post
		// related the it or on of its descendants.
		if ($document->getRecursivePublishedPostCount() <= 0)
		{
			return false;
		}
		return parent::isPublishable($document);
	}
	
	/**
	 * @param blog_persistentdocument_category $category
	 */
	public function incrementPublishedPostCount($category)
	{
		parent::incrementPublishedPostCount($category);
		
		// Refresh counter on ancestors. Here we can't just increment it because a category
		// and one of its ancestors may be related to a same post.
		$this->refreshRecursivePublishedPostCount($category);		
		$this->refreshRecursivePublishedPostCountOnAncestors($category);
	}

	/**
	 * @param blog_persistentdocument_category $category
	 */
	public function decrementPublishedPostCount($category)
	{
		parent::decrementPublishedPostCount($category);
		
		// Refresh counter on ancestors. Here we can't just decrement it because a category
		// and one of its ancestors may be related to a same post.
		$this->refreshRecursivePublishedPostCount($category);		
		$this->refreshRecursivePublishedPostCountOnAncestors($category);
	}
	
	/**
	 * @param blog_persistentdocument_category $category
	 */
	public function refreshPublishedPostCount($category)
	{
		$query = blog_PostService::getInstance()->createQuery()->add(Restrictions::published());
		$query->add(Restrictions::eq('category.id', $category->getId()));
		$query->setProjection(Projections::rowCount('count'));
		$result = $query->findUnique();
		$this->changePublishedPostCount($category, $result['count']);
	}

	/**
	 * @param blog_persistentdocument_category $category
	 * @param Integer $newCount
	 */
	private function changeRecursivePublishedPostCount($category, $newCount)
	{
		$oldCount = $category->getRecursivePublishedPostCount();
		if ($oldCount != $newCount)
		{
			try
			{
				$this->tm->beginTransaction();
				$category->setRecursivePublishedPostCount($newCount);
				$this->pp->updateDocument($category);
				if ($newCount == 0 || $oldCount == 0)
				{
					$this->publishDocumentIfPossible($category);
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
	 * @param blog_persistentdocument_category $category
	 */
	public function refreshRecursivePublishedPostCount($category)
	{
		$query = blog_PostService::getInstance()->createQuery()->add(Restrictions::published());
		$query->createCriteria('category')->add(Restrictions::orExp(
			Restrictions::eq('id', $category->getId()),
			Restrictions::descendentOf($category->getId())
		));
		$query->setProjection(Projections::rowCount('count'));
		$result = $query->findUnique();
		$this->changeRecursivePublishedPostCount($category, $result['count']);
	}
	
	/**
	 * @param blog_persistentdocument_category $category
	 */
	private function refreshRecursivePublishedPostCountOnAncestors($category)
	{
		foreach ($this->getAncestorsOf($category, 'modules_blog/category') as $ancestor)
		{
			$this->refreshRecursivePublishedPostCount($ancestor);
		}
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