<?php
/**
 * blog_BlogService
 * @package blog
 */
class blog_BlogService extends f_persistentdocument_DocumentService
{
	/**
	 * @var blog_BlogService
	 */
	private static $instance;

	/**
	 * @return blog_BlogService
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
	 * @return blog_persistentdocument_blog
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_blog/blog');
	}

	/**
	 * Create a query based on 'modules_blog/blog' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_blog/blog');
	}
	
	/**
	 * @param Integer $parentNodeId
	 * @return blog_persistentdocument_blog
	 */
	public function getByParentNodeId($parentNodeId)
	{
		$query = blog_BlogService::getInstance()->createQuery();
		$query->add(Restrictions::orExp(
			Restrictions::eq('id', $parentNodeId), 
			Restrictions::ancestorOf($parentNodeId)
		));
		return $query->findUnique();
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 * @param Array $date the result of getArchiveDates() or null
	 * @return blog_persistentdocument_post[]
	 */
	public function getSortedPosts($blog, $dates = null)
	{
		$query = blog_PostService::getInstance()->createQuery();
		$query->add(Restrictions::published());
		$query->add(Restrictions::eq('blog.id', $blog->getId()));
		if ($dates !== null && isset($dates['startDate']) && isset($dates['endDate']))
		{
			$query->add(Restrictions::between('postDate', $dates['startDate'], $dates['endDate']));
		}
		$query->addOrder(Order::desc('postdate'));
		return $query->find();
	}
	
	/**
	 * @param Integer $year
	 * @param Integer $month
	 * @return Array<'startDate' => ..., 'endDate' => ...>
	 */
	public function getArchiveDates($year, $month = null)
	{
		$month = (is_numeric($month)) ? intval($month) : null;
		if ($month !== null)
		{
			$dateCalendar = date_Calendar::getInstanceFromFormat($year.'/'.$month.'/1', 'Y/m/d');
			$startDate = $dateCalendar->toString();
			$startLabel = date_Formatter::format($dateCalendar, 'F Y');
			$endDate = $dateCalendar->add(date_Calendar::MONTH, 1)->sub(date_Calendar::SECOND, 1)->toString();
		}
		else 
		{
			$dateCalendar = date_Calendar::getInstanceFromFormat($year.'/1/1', 'Y/m/d');
			$startDate = $dateCalendar->toString();
			$startLabel = date_Formatter::format($dateCalendar, 'Y');
			$endDate = $dateCalendar->add(date_Calendar::YEAR, 1)->sub(date_Calendar::SECOND, 1)->toString();
		}
		
		return array('startLabel' => ucfirst($startLabel), 'startDate' => $startDate, 'endDate' => $endDate);
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 * @return Integer
	 */
	public function getPublishedPostCount($blog)
	{
		$query = blog_PostService::getInstance()->createQuery()
			->add(Restrictions::eq('blog.id', $blog->getId()))
			->add(Restrictions::published())
			->setProjection(Projections::rowCount('rows'));
		$result = $query->find();
		return intval($result[0]['rows']);
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 * @return blog_persistentdocument_post
	 */
	public function getLastPublishedPost($blog)
	{
		$query = blog_PostService::getInstance()->createQuery()
			->add(Restrictions::eq('blog.id', $blog->getId()))
			->add(Restrictions::published())
			->addOrder(Order::desc('postDate'))
			->setMaxResults(1);
		return f_util_ArrayUtils::firstElement($query->find());
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 * @return String[]
	 */
	public function getAuthorNames($blog)
	{
		$ps = blog_PostService::getInstance();
		$query = $ps->createQuery()
			->add(Restrictions::eq('blog.id', $blog->getId()))
			->add(Restrictions::published())
			->setProjection(Projections::property('authorid'), Projections::property('author'));
		$rows = $query->find();
		
		$authorNames = array();
		foreach ($rows as $row)
		{
			$authorName = $ps->getAuthorNameByAthorData($row['authorid'], $row['author']);
			if (!in_array($authorName, $authorNames))
			{
				$authorNames[] = $authorName;
			}
		}
		sort($authorNames);
		return $authorNames;
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 * @return rss_FeedWriter
	 */
	public function getRSSFeedWriter($blog)
	{
		$restriction = Restrictions::eq('blog.id', $blog->getId());
		return blog_PostService::getInstance()->getRSSFeedWriterByRestriction($restriction);
	}
		
	/**
	 * @param website_persistentdocument_website $website
	 * @return blog_persistentdocument_blog[]
	 */
	public function getByWebsite($website)
	{
		return $this->createQuery()->add(Restrictions::descendentOf($website->getId()))->find();
	}

	/**
	 * @param blog_persistentdocument_blog $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
	protected function postInsert($document, $parentNodeId)
	{
		$postFolder = blog_PostfolderService::getInstance()->getNewDocumentInstance();
		$postFolder->setLabel(f_Locale::translate('&modules.blog.document.postfolder.Default-label-value;'));
		$postFolder->save($document->getId());
		
		$categoryFolder = blog_CategoryfolderService::getInstance()->getNewDocumentInstance();
		$categoryFolder->setLabel(f_Locale::translate('&modules.blog.document.categoryfolder.Default-label-value;'));
		$categoryFolder->save($document->getId());
		
		$keywordFolder = blog_KeywordfolderService::getInstance()->getNewDocumentInstance();
		$keywordFolder->setLabel(f_Locale::translate('&modules.blog.document.keywordfolder.Default-label-value;'));
		$keywordFolder->save($document->getId());
	}

	/**
	 * @param blog_persistentdocument_blog $document
	 * @return void
	 */
	protected function preDelete($document)
	{
		// Delete folders.
		foreach ($this->getChildrenOf($document) as $child)
		{
			$child->delete();
		}
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param string $forModuleName
	 * @param array $allowedSections
	 * @return array
	 */
	public function getResume($document, $forModuleName, $allowedSections = null)
	{
		$data = parent::getResume($document, $forModuleName, $allowedSections);
		$data['posts']['postcount'] = $this->getPublishedPostCount($document);
		$lastPost = $this->getLastPublishedPost($document);
		if ($lastPost !== null)
		{
			$data['posts']['lastpublishedpost'] = LinkHelper::getDocumentUrl($lastPost);
			
		}
		$authorNames = $this->getAuthorNames($document);
		if (f_util_ArrayUtils::isNotEmpty($authorNames))
		{
			$data['posts']['authors'] = f_util_StringUtils::shortenString(implode(", ", $authorNames), 80);
		}
		$mostUsedKeyWords = blog_KeywordService::getInstance()->getMostUsedByBlog($document, 5);
		if (f_util_ArrayUtils::isNotEmpty($mostUsedKeyWords))
		{
			$keyWords = array();
			foreach ($mostUsedKeyWords as $mostUsedKeyWord)
			{
				$keyWords[] = $mostUsedKeyWord->getLabel();
			}
			$data['posts']['keywords'] = implode(", ", $keyWords);
		}
		
		return $data;
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 */
	public function pingServicesForBlog($blog)
	{
		if ($blog->getPingurls() === null)
		{
			return;
		}
		
		$website = DocumentHelper::getDocumentInstance($this->getWebsiteId($blog));
		$websiteLabel = $website->getLabel();
		$websiteUrl = LinkHelper::getDocumentUrl($website);
		$blogUrl = LinkHelper::getDocumentUrl($blog);
		$feedUrl = LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $blog->getId()));
		
		foreach ($blog->getPingurlsArray() as $url)
		{
			try 
			{
				$client = new Zend_XmlRpc_Client($url, change_HttpClientService::getInstance()->getNewHttpClient());  
				$proxy = $client->getProxy('weblogUpdates');
				$result = $proxy->extendedPing($websiteLabel, $websiteUrl , $blogUrl , $feedUrl);
				if (isset($result['flerror']) && $result['flerror'] != 0)
				{
					Framework::warn(__METHOD__ . var_export($result, true));
				}
			}
			catch (Exception $e)
			{
				Framework::exception($e);
			}
		}
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 */
	public function deleteWithContents($blog)
	{
		try 
		{
			$this->tm->beginTransaction();
			
			TreeService::getInstance()->setTreeNodeCache(false);
			
			foreach (blog_PostService::getInstance()->createQuery()->add(Restrictions::eq('blog', $blog))->find() as $post)
			{
				$post->setMonth(null);
				$post->removeAllCategory();
				$post->removeAllKeyword();
				$post->delete();
			}
			blog_PostfolderService::getInstance()->createQuery()->add(Restrictions::descendentOf($blog->getId()))->delete();
			
			$this->deleteWithChildrenRecursive(blog_CategoryfolderService::getInstance()->getByBlog($blog));
			
			blog_KeywordService::getInstance()->createQuery()->add(Restrictions::eq('blog', $blog))->delete();
			blog_KeywordfolderService::getInstance()->createQuery()->add(Restrictions::descendentOf($blog->getId()))->delete();
			
			foreach (blog_MonthService::getInstance()->createQuery()->add(Restrictions::eq('blog', $blog))->find() as $month)
			{
				$month->setYear(null);
				$month->delete();
			}
			blog_YearService::getInstance()->createQuery()->add(Restrictions::eq('blog', $blog))->delete();
			
			$blog->delete();
			
			$this->tm->commit();
		}
		catch (Exception $e)
		{
			$this->tm->rollBack($e);
		}
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $parent
	 */
	private function deleteWithChildrenRecursive($parent)
	{
		foreach ($parent->getDocumentService()->getChildrenOf($parent) as $category)
		{
			$this->deleteWithChildrenRecursive($category);
		}
		$parent->delete();
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