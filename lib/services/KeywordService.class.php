<?php
/**
 * blog_KeywordService
 * @package blog
 */
class blog_KeywordService extends blog_PostgroupService
{
	/**
	 * @var blog_KeywordService
	 */
	private static $instance;

	/**
	 * @return blog_KeywordService
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
	 * @return blog_persistentdocument_keyword
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_blog/keyword');
	}

	/**
	 * Create a query based on 'modules_blog/keyword' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_blog/keyword');
	}
	
	/**
	 * Create a query based on 'modules_blog/keyword' model.
	 * Only documents that are strictly instance of modules_blog/keyword
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_blog/keyword', false);
	}
	
	/**
	 * @param blog_persistentdocument_keyword $keyword
	 */
	public function getSortedPosts($keyword)
	{
		$query = blog_PostService::getInstance()->createQuery();
		$query->add(Restrictions::published());
		$query->add(Restrictions::eq('keyword.id', $keyword->getId()));
		$query->addOrder(Order::desc('postdate'));
		return $query->find();
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 * @param Interger $limit
	 */
	public function getMostUsedByBlog($blog, $limit)
	{
		$query = $this->createQuery();
		$query->add(Restrictions::published());
		$query->add(Restrictions::eq('blog.id', $blog->getId()));
		$query->addOrder(Order::desc('publishedpostcount'));
		$query->setMaxResults($limit);
		return $query->find();
	}
	
	/**
	 * @param blog_persistentdocument_keyword $keyword
	 * @return rss_FeedWriter
	 */
	public function getRSSFeedWriter($keyword)
	{
		$restriction = Restrictions::eq('keyword.id', $keyword->getId());
		return blog_PostService::getInstance()->getRSSFeedWriterByRestriction($restriction);
	}
	
	/**
	 * @param blog_persistentdocument_keyword $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
//	protected function preSave($document, $parentNodeId = null)
//	{
//		parent::preSave($document, $parentNodeId);
//
//	}

	/**
	 * @param blog_persistentdocument_keyword $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
	protected function preInsert($document, $parentNodeId = null)
	{
		parent::preInsert($document, $parentNodeId);
		
		$document->setInsertInTree(false);
	}

	/**
	 * @param blog_persistentdocument_keyword $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postInsert($document, $parentNodeId = null)
//	{
//		parent::postInsert($document, $parentNodeId);
//	}

	/**
	 * @param blog_persistentdocument_keyword $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preUpdate($document, $parentNodeId = null)
//	{
//		parent::preUpdate($document, $parentNodeId);
//	}

	/**
	 * @param blog_persistentdocument_keyword $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postUpdate($document, $parentNodeId = null)
//	{
//		parent::postUpdate($document, $parentNodeId);
//	}

	/**
	 * @param blog_persistentdocument_keyword $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postSave($document, $parentNodeId = null)
//	{
//		parent::postSave($document, $parentNodeId);
//	}

	/**
	 * @param blog_persistentdocument_keyword $document
	 * @return void
	 */
//	protected function preDelete($document)
//	{
//		parent::preDelete($document);
//	}

	/**
	 * @param blog_persistentdocument_keyword $document
	 * @return void
	 */
//	protected function preDeleteLocalized($document)
//	{
//		parent::preDeleteLocalized($document);
//	}

	/**
	 * @param blog_persistentdocument_keyword $document
	 * @return void
	 */
//	protected function postDelete($document)
//	{
//		parent::postDelete($document);
//	}

	/**
	 * @param blog_persistentdocument_keyword $document
	 * @return void
	 */
//	protected function postDeleteLocalized($document)
//	{
//		parent::postDeleteLocalized($document);
//	}

	/**
	 * @param blog_persistentdocument_keyword $document
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
	 * @param blog_persistentdocument_keyword $document
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
	 * @param blog_persistentdocument_keyword $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagAdded($document, $tag)
//	{
//		parent::tagAdded($document, $tag);
//	}

	/**
	 * @param blog_persistentdocument_keyword $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagRemoved($document, $tag)
//	{
//		parent::tagRemoved($document, $tag);
//	}

	/**
	 * @param blog_persistentdocument_keyword $fromDocument
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
	 * @param blog_persistentdocument_keyword $toDocument
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
//	protected function onMoveToStart($document, $destId)
//	{
//		parent::onMoveToStart($document, $destId);
//	}

	/**
	 * @param blog_persistentdocument_keyword $document
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
	 * @param String $label
	 * @param blog_persistentdocument_blog $blog
	 * @return blog_persistentdocument_keyword
	 */
	public function getByLabelInBlog($label, $blog)
	{
		$query = $this->createStrictQuery();
		$query->add(Restrictions::eq('label', $label, true));
		$query->add(Restrictions::eq('blog.id', $blog->getId()));
		return $query->findUnique();
	}
	
	/**
	 * @param blog_persistentdocument_keyword $document
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
	 * @param blog_persistentdocument_keyword $keyword
	 */
	public function refreshPublishedPostCount($keyword)
	{
		$query = blog_PostService::getInstance()->createQuery()->add(Restrictions::published());
		$query->add(Restrictions::eq('keyword.id', $keyword->getId()));
		$query->setProjection(Projections::rowCount('count'));
		$result = $query->findUnique();
		$this->changePublishedPostCount($keyword, $result['count']);
	}
	
	/**
	 * @param blog_persistentdocument_keyword $keyword
	 */
	public function incrementPostCount($keyword)
	{
		$this->changePostCount($keyword, $keyword->getPostCount()+1);
	}

	/**
	 * @param blog_persistentdocument_keyword $keyword
	 */
	public function decrementPostCount($keyword)
	{
		$this->changePostCount($keyword, $keyword->getPostCount()-1);
	}

	/**
	 * @param blog_persistentdocument_keyword $keyword
	 * @param Integer $newCount
	 */
	private function changePostCount($keyword, $newCount)
	{
		$oldCount = $keyword->getPostCount();
		if ($oldCount != $newCount)
		{
			$keyword->setPostCount($newCount);
			$this->pp->updateDocument($keyword);
		}
	}
	
	/**
	 * @param blog_persistentdocument_keyword $keyword
	 */
	public function refreshPostCount($keyword)
	{
		$query = blog_PostService::getInstance()->createQuery();
		$query->add(Restrictions::eq('keyword.id', $keyword->getId()));
		$query->setProjection(Projections::rowCount('count'));
		$result = $query->findUnique();
		$this->changePostCount($keyword, $result['count']);
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
}