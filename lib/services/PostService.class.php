<?php
/**
 * blog_PostService
 * @package modules.blog
 */
class blog_PostService extends f_persistentdocument_DocumentService
{
	const TARGET_PINGBACK_OK = 1;
	const TARGET_PINGBACK_FAILED = -1;
	const TARGET_PINGBACK_NOT_AVAILABLE = 0;
	
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
		
		// Update month field.
		if ($document->isPropertyModified('postDate'))
		{
			$this->updateMonth($document);
		}
		
		// Handle trackbacks.
		if ($document->isPropertyModified('trackbacks'))
		{
			$document->setMeta('trackbacks.modified', true);
		}
	}
	
	/**
	 * @param blog_persistentdocument_post $post
	 */
	private function buildLinklist($post)
	{
		$DOMDoc = f_util_DOMUtils::fromXhtmlFragmentString(f_util_HtmlUtils::renderHtmlFragment($post->getContents()));
					
		$nodes = $DOMDoc->find('//a[@href]');
		$linklist = array();
		foreach ($nodes as $node)
		{
			$href = $node->getAttribute("href");
			if (!in_array($href, $linklist) && !$this->isLocalUrl($href))
			{
				$linklist[] = $href;
			}
		}
		$post->setMeta('linklist', $linklist);
		$post->saveMeta();
	}
	
	/**
	 * @var Array
	 */
	private $websiteDomains = null;
	
	/**
	 * @param String $url
	 * @return Boolean
	 */
	protected function isLocalUrl($url)
	{
		if ($this->websiteDomains === null)
		{
			$this->websiteDomains = website_WebsiteService::getInstance()->createQuery()->setProjection(Projections::property('domain'))->findColumn('domain');
		}
		$href = preg_replace('#^(http|https)://#', '', $url);
		foreach ($this->websiteDomains as $domain)
		{
			if (strpos($href, $domain) === 0)
			{
				return true;
			}
			if (strpos($href, str_replace('www.', '', $domain)) === 0)
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @see f_persistentdocument_DocumentService::postUpdate()
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param Integer $parentNodeId
	 */
	protected function postUpdate($document, $parentNodeId)
	{
		if ($document->isPublished())
		{
			$this->schedulePings($document);
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
	protected function preInsert($document, $parentNodeId)
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
	 */
	private function syncKeywordsAndCategoriesForDocument($document)
	{
		foreach ($document->getCategoryArray() as $category) 
		{
			$cs = blog_CategoryService::getInstance();
			$cs->refreshPublishedPostCount($category);
		}
		
		foreach ($document->getKeywordArray() as $keyWord) 
		{
			$ks = blog_KeywordService::getInstance();
			$ks->refreshPostCount($keyWord);
			$ks->refreshPublishedPostCount($keyWord);
		}	
	}
	
	/**
	 * @param blog_persistentdocument_post $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
	protected function postSave($document, $parentNodeId)
	{
		// Delete old month if unused. This must be done is post save to nested saves. 
		$ms = blog_MonthService::getInstance();
		$oldMonthId = $document->getMonthOldValueId();
		if ($oldMonthId !== null)
		{
			$oldMonth = DocumentHelper::getDocumentInstance($oldMonthId);
			$newMonth = $document->getMonth();
			if (!DocumentHelper::equals($newMonth, $oldMonth))
			{
				$ms->deleteIfUnused($oldMonth, $document->getId());
			}
		}
		$this->syncKeywordsAndCategoriesForDocument($document);
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

			
			// Generate postDate if it is null.
			// Here we do not need to update published post count on month because it is done on presave.
			if ($document->getPostDate() === null)
			{
				$document->setPostDate(date_Calendar::now()->toString());
				$this->getPersistentProvider()->updateDocument($document);
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
			$this->schedulePingbacksAndTrackbacks($document);
		}
		// Status transits from PUBLICATED to ACTIVE.
		elseif ($oldPublicationStatus == 'PUBLICATED')
		{	
			// Update counter on month.
			$month = $document->getMonth();
			if ($month !== null)
			{
				blog_MonthService::getInstance()->decrementPublishedPostCount($month);
			}
			blog_BlogService::getInstance()->pingServicesForBlog($document->getBlog());
		}
		$this->syncKeywordsAndCategoriesForDocument($document);
	}
	
	/**
	 * @param blog_persistentdocument_post $document
	 */
	protected function schedulePingbacksAndTrackbacks($document)
	{
		$this->buildLinklist($document);
		$linkList = array();
		if ($document->hasMeta('linklist'))
		{
			$linkList = $document->getMeta('linklist');
		}
		$pingList = array();
		if ($document->hasMeta('pingbackresults'))
		{
			$pingList = array_keys($document->getMeta('pingbackresults'));
		}
		if (!$document->hasMeta('trackbacks.modified') && f_util_ArrayUtils::isEmpty(array_diff($linkList, $pingList)))
		{
			if (Framework::isDebugEnabled())
			{
				Framework::debug(__METHOD__ . ": no new link in post, nothing to ping");
			}
			return;
		}
		$document->setMeta('trackbacks.modified', null);
		$document->saveMeta();
		
		$taskService = task_PlannedtaskService::getInstance();
		$plannedTasks = $taskService->getRunnableBySystemtaskclassname('blog_PingBlogsTask');
		if (f_util_ArrayUtils::isNotEmpty($plannedTasks))
		{
			$pingTask = f_util_ArrayUtils::firstElement($plannedTasks);
			$parameters = unserialize($pingTask->getParameters());
			$postIds = $parameters['postIds'];
		}
		else
		{
			$pingTask = $taskService->getNewDocumentInstance();
			$pingTask->setSystemtaskclassname('blog_PingBlogsTask');
			$pingTask->setLabel(__METHOD__);
			$postIds = array();
		}
		if (!in_array($document->getId(), $postIds))
		{
			$postIds[] = $document->getId();
		}
		$pingTask->setParameters(serialize(array('postIds' => $postIds)));
		$pingTask->setUniqueExecutiondate(date_Calendar::getInstance());
		$pingTask->save(ModuleService::getInstance()->getSystemFolderId('task', 'blog'));
	}
	
	/**
	 * @param blog_persistentdocument_post $post
	 */
	public function pingbacksForPost($post)
	{
		$cs = blog_PingBackClientService::getInstance();
		$linkList = $post->getMeta('linklist');
		$pingbackResults = array();
		if ($post->hasMeta('pingbackresults'))
		{
			$pingbackResults = $post->getMeta('pingbackresults');
		}
		$pingList = array_keys($pingbackResults);
		foreach (array_diff($linkList, $pingList) as $url)
		{
			$pbUrl = $cs->getPingbackUrlForUrl($url);
			if ($pbUrl !== null)
			{
				if (Framework::isDebugEnabled())
				{
					Framework::debug(__METHOD__ . ': getting ready to ping ' . $pbUrl . 'for postId = ' . $post->getId());
				}
				try
				{
					$cs->ping($pbUrl, LinkHelper::getDocumentUrl($post), $url);
					$pingbackResults[$url] = array('status' => self::TARGET_PINGBACK_OK);
				}
				catch (Exception $e)
				{
					$pingbackResults[$url] = array('status' => self::TARGET_PINGBACK_FAILED, 'message' => $e->getMessage());
				}
			}
			else
			{
				$pingbackResults[$url] = array('status' => self::TARGET_PINGBACK_NOT_AVAILABLE);
			}
		}
		$post->setMeta('pingbackresults', $pingbackResults);
		$post->saveMeta();
	}
	

	/**
	 * @param blog_persistentdocument_post $post
	 */
	public function trackbacksForPost($post)
	{
		$trackbackResults = array();
		if ($post->hasMeta('trackbackresults'))
		{
			$trackbackResults = $post->getMeta('trackbackresults');
		}
		$trackbackUrls = $post->getTrackbacks();
		$trackbacks = explode(',', $trackbackUrls);
		$postUrl = LinkHelper::getDocumentUrl($post);
		foreach (array_diff($trackbacks, array_keys($trackbackResults)) as $url)
		{
			$url = trim($url);
			if ($this->isLocalUrl($url))
			{
				continue;
			}
			
			$client = HTTPClientService::getInstance()->getNewHTTPClient();
			$params = array('url' => $postUrl);
			$params['blog_name'] = $post->getBlogLabel();
			$params['title'] = $post->getLabel();
			$summary = f_util_StringUtils::htmlToText($post->getSummary());
			if (f_util_StringUtils::isEmpty($summary))
			{
				$summary = f_util_StringUtils::shortenString(f_util_StringUtils::htmlToText($post->getContents()));
			}
			$params['excerpt'] = $summary;
			$data = $client->post($url, $params);
			$trackbackResults[$url] = $data;
		}
		$post->setMeta('trackbackresults', $trackbackResults);
		$post->saveMeta();
	}
	
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
	 * @param blog_persistentdocument_post $document
	 * @param string $forModuleName
	 * @param array $allowedSections
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
		return $query->setProjection(Projections::property('id'))->setMaxResults($maxUrl)->findColumn('id');
	}
	
	/**
	 * @see f_persistentdocument_DocumentService::getDisplayPage()
	 * @param blog_persistentdocument_post $document
	 * @return website_persistentdocument_page
	 */
	public function getDisplayPage($document)
	{
		//Check for original document;
		$document = DocumentHelper::getByCorrection($document);
		if ($document->isPublished())
		{
			$blog = $document->getBlog();
			$page = TagService::getInstance()->getDocumentBySiblingTag('functional_blog_post-detail', $blog);
			return $page;
		}
		return parent::getDisplayPage($document);
	}
	
	/**
	 * @param blog_persistentdocument_post $document
	 * @return website_persistentdocument_page
	 */
	public function getPreviewPage($document)
	{
		// Check for original document.
		$document = DocumentHelper::getByCorrection($document);
		$blog = $document->getBlog();
		return TagService::getInstance()->getDocumentBySiblingTag('functional_blog_post-detail', $blog);
	}
	
	/**
	 * @param blog_persistentdocument_post $document
	 * @return integer | null
	 */
	public function getWebsiteId($document)
	{
		$blog = $document->getBlog();
		return $blog->getDocumentService()->getWebsiteId($blog);
	}
	
	// Tweets handling.
	
	/**
	 * @param blog_persistentdocument_post $document or null
	 * @param integer $websiteId
	 * @return array
	 */
	public function getReplacementsForTweet($document, $websiteId)
	{
		$label = array(
			'name' => 'label',
			'label' => f_Locale::translateUI('&modules.blog.document.post.Label;'),
			'maxLength' => 80
		);
		$shortUrl = array(
			'name' => 'shortUrl', 
			'label' => f_Locale::translateUI('&modules.twitterconnect.bo.general.Short-url;'),
			'maxLength' => 30
		);
		if ($document !== null)
		{
			$label['value'] = f_util_StringUtils::shortenString($document->getLabel(), 80);
			$shortUrl['value'] = website_ShortenUrlService::getInstance()->shortenUrl(LinkHelper::getDocumentUrl($document));
		}
		return array($label, $shortUrl);
	}
	
	/**
	 * @param blog_persistentdocument_post $document
	 * @return f_persistentdocument_PersistentDocument[]
	 */
	public function getContainersForTweets($document)
	{
		$containers = $document->getCategoryArray();
		$containers[] = $document->getBlog();
		return $containers;
	}
}