<?php
/**
 * blog_PostService
 * @package blog
 */
class blog_PostService extends f_persistentdocument_DocumentService
{
	/**
	 * @var blog_PostService
	 */
	private static $instance;

	/**
	 * @return blog_PostService
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
	 * @return blog_persistentdocument_post
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_blog/post');
	}

	/**
	 * Create a query based on 'modules_blog/post' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_blog/post');
	}

	/**
	 * @param blog_persistentdocument_post $post
	 * @return String
	 */
	public function getAuthorName($post)
	{
		return $this->getAuthorNameByAthorData($post->getAuthorid(), $post->getAuthor());
	}

	/**
	 * @param Integer $authorid
	 * @param String $author
	 * @return String
	 */
	public function getAuthorNameByAthorData($authorid, $author)
	{
		try 
		{
			$authorDocument = DocumentHelper::getDocumentInstance($authorid);
			return $authorDocument->getFullname();
		}
		catch (Exception $e)
		{
			// The author may not exist any more.
			return $author;
		}
	}
	
	/**
	 * @param blog_persistentdocument_post $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId = null)
	{
		if ($document->getBlog() === null)
		{
			$document->setBlog(blog_BlogService::getInstance()->getByParentNodeId($parentNodeId));
		}
		
		// Remove categories from other blogs.
		$blogId = $document->getBlog()->getId();
		foreach ($document->getCategoryArray() as $category)
		{
			if ($category->getBlog()->getId() != $blogId)
			{
				$document->removeCategory($category);	
			}
		}
		
		$this->synchronizeKeywordProperties($document);
		
		if ($document->isPropertyModified('keyword'))
		{
			$oldIds = $document->getKeywordOldValueIds();
			$currentKeywords = $document->getKeywordArray();
			$ts = blog_KeywordService::getInstance();
			
			// Increment post count for added keywords.
			$currentIds = array();
			foreach ($currentKeywords as $keyword)
			{
				if (!in_array($keyword->getId(), $oldIds))
				{
					$ts->incrementPostCount($keyword);
					if ($document->isPublished())
					{
						$ts->incrementPublishedPostCount($keyword);
					}
				}
				$currentIds[] = $keyword->getId();
			}
			
			// Decrement post count for removed keywords.
			foreach ($oldIds as $keywordId)
			{
				if (!in_array($keywordId, $currentIds))
				{
					$keyword = DocumentHelper::getDocumentInstance($keywordId);
					$ts->decrementPostCount($keyword);
					if ($document->isPublished())
					{
						$ts->decrementPublishedPostCount($keyword);
					}
				}
			}
		}
		
		// Update month field.
		if ($document->isPropertyModified('postDate'))
		{
			$this->updateMonth($document);	
		}
	}
	

	/**
	 * @see f_persistentdocument_DocumentService::postUpdate()
	 *
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param Integer $parentNodeId
	 */
	protected function postUpdate($document, $parentNodeId)
	{
		if ($document->isPublished())
		{
			
		}
	}

	
	/**
	 * @param blog_persistentdocument_post $post
	 */
	public function updateMonth($post)
	{
		$newDate = $post->getPostDate();
		if ($newDate !== null)
		{
			$newDate = date_Calendar::getInstance($newDate);
		}

		$ms = blog_MonthService::getInstance();
		$oldMonth = $post->getMonth();
		$newMonth = $ms->getByDateAndBlog($newDate, $post->getBlog());
			
		if (!DocumentHelper::equals($newMonth, $oldMonth))
		{
			// Set the new month.
			$post->setMonth($newMonth);
			
			// Update published post count.
			if ($post->isPublished())
			{
				if ($newMonth !== null)
				{
					$ms->incrementPublishedPostCount($newMonth);
				}
				if ($oldMonth !== null)
				{
					$ms->decrementPublishedPostCount($oldMonth);
				}
			}
		}
	}

	/**
	 * @param blog_persistentdocument_post $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
	protected function preInsert($document, $parentNodeId = null)
	{
		$document->setInsertInTree(false);
		
		// Set the full name of the current user as author, to display it in frontoffice.
		if (RequestContext::getInstance()->getMode() == RequestContext::BACKOFFICE_MODE)
		{
			$user = users_UserService::getInstance()->getCurrentBackEndUser();
		}
		else
		{
			$user = users_UserService::getInstance()->getCurrentFrontEndUser();
		}
		
		if ($user !== null)
		{
			$document->setAuthor($user->getFullname());
		}
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 * @return rss_FeedWriter
	 */
	public function getRSSFeedWriterByRestriction($restriction)
	{
		$query = $this->createQuery();
		$query->add(Restrictions::published());
		$query->add($restriction);
		$limit = ModuleService::getInstance()->getPreferenceValue('blog', 'rssMaxItemCount');
		if ($limit > 0)
		{
			$query->setMaxResults($limit);
		}
		$query->addOrder(Order::desc('postDate'));
		
		$writer = new rss_FeedWriter();
		foreach ($query->find() as $post)
		{
			$writer->addItem($post);
		}
		return $writer;
	}
	
	/**
	 * @param blog_persistentdocument_post $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postInsert($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param blog_persistentdocument_post $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preUpdate($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param blog_persistentdocument_post $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postUpdate($document, $parentNodeId = null)
//	{
//	}

	/**
	 * @param blog_persistentdocument_post $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
	protected function postSave($document, $parentNodeId = null)
	{
		// Delete old month if unused. This must be done is post save to nested saves. 
		$ms = blog_MonthService::getInstance();
		$oldMonthId = $document->getMonthOldValueId();
		Framework::debug(__METHOD__ . ' ' . $oldMonthId);
		if ($oldMonthId !== null)
		{
			$oldMonth = DocumentHelper::getDocumentInstance($oldMonthId);
			$newMonth = $document->getMonth();
			if (!DocumentHelper::equals($newMonth, $oldMonth))
			{
				$ms->deleteIfUnused($oldMonth, $document->getId());
			}
		}
		
		// Update categories counters.
		// This must be done here, because recursive post count is
		// refreshed by a query so, the update must be done at this time.
		if ($document->isPropertyModified('category') && $document->isPublished())
		{
			$oldIds = $document->getCategoryOldValueIds();
			$currentCategories = $document->getCategoryArray();
			$cs = blog_CategoryService::getInstance();
			
			// Increment post count for added categories.
			$currentIds = array();
			foreach ($currentCategories as $category)
			{
				if (!in_array($category->getId(), $oldIds))
				{
					$cs->incrementPublishedPostCount($category);
				}
				$currentIds[] = $category->getId();
			}
			
			// Decrement post count for removed categories.
			foreach ($oldIds as $categoryId)
			{
				if (!in_array($categoryId, $currentIds))
				{
					$category = DocumentHelper::getDocumentInstance($categoryId);
					$cs->decrementPublishedPostCount($category);
				}
			}
		}
	}

	/**
	 * @param blog_persistentdocument_post $document
	 * @return void
	 */
	protected function preDelete($document)
	{
		$ts = blog_KeywordService::getInstance();
		foreach ($document->getKeywordArray() as $keyword)
		{
			$ts->decrementPostCount($keyword);
		}
	}

	/**
	 * @param blog_persistentdocument_post $document
	 * @return void
	 */
//	protected function preDeleteLocalized($document)
//	{
//	}

	/**
	 * @param blog_persistentdocument_post $document
	 * @return void
	 */
//	protected function postDelete($document)
//	{
//	}

	/**
	 * @param blog_persistentdocument_post $document
	 * @return void
	 */
//	protected function postDeleteLocalized($document)
//	{
//	}

	/**
	 * @param blog_persistentdocument_post $document
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
	 * @param blog_persistentdocument_post $document
	 * @param String $oldPublicationStatus
	 * @param array<"cause" => String, "modifiedPropertyNames" => array, "oldPropertyValues" => array> $params
	 * @return void
	 */
	protected function publicationStatusChanged($document, $oldPublicationStatus, $params)
	{
		// Status transits from ACTIVE to PUBLICATED.
		if ($document->isPublished())
		{
			// Update counters in keywords and categories.
			$cs = blog_CategoryService::getInstance();
			foreach ($document->getCategoryArray() as $category)
			{
				$cs->incrementPublishedPostCount($category);
			}
			
			$ts = blog_KeywordService::getInstance();
			foreach ($document->getKeywordArray() as $keyword)
			{
				$ts->incrementPublishedPostCount($keyword);
			}			
			
			// Generate postDate if it is null.
			// Here we do not need to update published post count on month because it is done on presave.
			if ($document->getPostDate() === null)
			{
				$document->setPostDate(date_Calendar::now()->toString());
				$document->save();
			}
			// Update count on month.
			else
			{
				$month = $document->getMonth();
				if ($month !== null)
				{
					blog_MonthService::getInstance()->incrementPublishedPostCount($month);
				}
			}
			blog_BlogService::getInstance()->pingServicesForBlog($document->getBlog());
		}
		// Status transits from PUBLICATED to ACTIVE.
		elseif ($oldPublicationStatus == 'PUBLICATED')
		{
			// Update counters in keywords and categories.
			$cs = blog_CategoryService::getInstance();
			foreach ($document->getCategoryArray() as $category)
			{
				$cs->decrementPublishedPostCount($category);
			}
			
			$ts = blog_KeywordService::getInstance();
			foreach ($document->getKeywordArray() as $keyword)
			{
				$ts->decrementPublishedPostCount($keyword);
			}
			
			// Update counter on month.
			$month = $document->getMonth();
			if ($month !== null)
			{
				blog_MonthService::getInstance()->decrementPublishedPostCount($month);
			}
			blog_BlogService::getInstance()->pingServicesForBlog($document->getBlog());
		}
		
		
	}

	/**
	 * Correction document is available via $args['correction'].
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param Array<String=>mixed> $args
	 */
//	protected function onCorrectionActivated($document, $args)
//	{
//	}

	/**
	 * @param blog_persistentdocument_post $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagAdded($document, $tag)
//	{
//	}

	/**
	 * @param blog_persistentdocument_post $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagRemoved($document, $tag)
//	{
//	}

	/**
	 * @param blog_persistentdocument_post $fromDocument
	 * @param f_persistentdocument_PersistentDocument $toDocument
	 * @param String $tag
	 * @return void
	 */
//	public function tagMovedFrom($fromDocument, $toDocument, $tag)
//	{
//	}

	/**
	 * @param f_persistentdocument_PersistentDocument $fromDocument
	 * @param blog_persistentdocument_post $toDocument
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
	 * @param blog_persistentdocument_post $document
	 */
	private function synchronizeKeywordProperties($document)
	{
		if ($document->isPropertyModified('keywordsText'))
		{
			// Clear the keyword property.
			$document->setKeywordArray(array());
			
			// Add all keywords from the keywordsText property.
			$ts = blog_KeywordService::getInstance();
			$blog = $document->getBlog();
			foreach (explode(',', $document->getKeywordsText()) as $keywordLabel)
			{
				$keywordLabel = trim($keywordLabel);
				if ($keywordLabel != '')
				{
					$keyword = $ts->getByLabelInBlog($keywordLabel, $blog);
					if ($keyword === null)
					{
						$keyword = $ts->getNewDocumentInstance();
						$keyword->setLabel($keywordLabel);
						$keyword->save($blog->getId());
					}
					$document->addKeyword($keyword);
				}
			}		
		}
		
		if ($document->isPropertyModified('keyword'))
		{
			$labels = array();
			foreach ($document->getKeywordArray() as $keyword)
			{
				$labels[] = $keyword->getLabel();
			}
			$document->setKeywordsText(implode(', ', $labels));
		}
	}
	
	/**
	 * @see f_persistentdocument_DocumentService::getResume()
	 *
	 * @param blog_persistentdocument_post $document
	 * @param string $forModuleName
	 * @param unknown_type $allowedSections
	 * @return array
	 */
	public function getResume($document, $forModuleName, $allowedSections = null)
	{
		$data = parent::getResume($document, $forModuleName, $allowedSections);
		$data['properties']['keywordsText'] = $document->getKeywordsText();
		return $data;
	}
	
	/**
	 * @param website_persistentdoculent_website $website
	 * @param Integer $maxUrl
	 * @return Integer[]
	 */
	public function getIdsForSitemap($website, $maxUrl)
	{
		$query = $this->createQuery()->add(Restrictions::published());
		$query->createCriteria('blog')->add(Restrictions::descendentOf($website->getId()));
		return $query->setProjection(Projections::property('id'))->findColumn('id');	
	}
}