<?php
/**
 * blog_persistentdocument_post
 * @package blog.persistentdocument
 */
class blog_persistentdocument_post extends blog_persistentdocument_postbase implements indexer_IndexableDocument, rss_Item
{
	/**
	 * Get the indexable document
	 *
	 * @return indexer_IndexedDocument
	 */
	public function getIndexedDocument()
	{
		$indexedDoc = new indexer_IndexedDocument();
		$indexedDoc->setId($this->getId());
		$indexedDoc->setDocumentModel('modules_blog/post');
		$indexedDoc->setLabel($this->getLabel());
		$indexedDoc->setLang($this->getLang());
		$indexedDoc->setText($this->getFullTextForIndexation());
		return $indexedDoc;
	}
	
	/**
	 * @return String
	 */
	private function getFullTextForIndexation()
	{
		$fullText = $this->getAuthorName();
		foreach ($this->getCategoryArray() as $category)
		{
			$fullText .= ' ' . $category->getLabel();
		}
		$fullText .= ' ' . $this->getKeywordsText();
		$fullText .= ' ' . $this->getSummaryAsHtml();
		$fullText .= ' ' . $this->getContentsAsHtml();
		return f_util_StringUtils::htmlToText($fullText);
	}
	
	/**
	 * @return String
	 */
	public function getAuthorName()
	{
		return $this->getDocumentService()->getAuthorName($this);
	}
	
	/**
	 * @return String
	 */
	public function getCommentCount()
	{
		return comment_CommentService::getInstance()->getPublishedCountByTargetId($this->getId());
	}
	
	/**
	 * @return Integer
	 */
	public function getBlogId()
	{
		return $this->getBlog()->getId();
	}
	
	/**
	 * @return String
	 */
	public function getBlogLabel()
	{
		return $this->getBlog()->getLabel();
	}
	
	/**
	 * @return String
	 */
	public function getRSSLabel()
	{
		return $this->getLabel();
	}
	
	/**
	 * @return String
	 */
	public function getRSSDescription()
	{
		$description = $this->getSummaryAsHtml();
		if (!$description)
		{
			$description = $this->getContentsAsHtml();
		}
		return $description;
	}
	
	/**
	 * @return String
	 */
	public function getRSSGuid()
	{
		return LinkHelper::getDocumentUrl($this);
	}
	
	/**
	 * @return String
	 */
	public function getRSSDate()
	{
		return $this->getPostDate();
	}
	
	/**
	 * @see blog_persistentdocument_postbase::getAllowPingbacks()
	 *
	 * @return Boolean
	 */
	public function getAllowPingbacks()
	{
		$blog = $this->getBlog();
		if ($blog !== null && $blog->getEnablepingback())
		{
			return parent::getAllowPingbacks();
		}
		return false;
	}
	
	/**
	 * @see blog_persistentdocument_postbase::getAllowTrackbacks()
	 *
	 * @return Boolean
	 */
	public function getAllowTrackbacks()
	{
		$blog = $this->getBlog();
		if ($blog !== null && $blog->getEnabletrackback())
		{
			return parent::getAllowTrackbacks();
		}
		return false;
	}

	/**
	 * @param string $actionType
	 * @param array $formProperties
	 */
	public function addFormProperties($propertiesNames, &$formProperties)
	{	
		if (!$this->isNew())
		{
			$formProperties['blogId'] = $this->getBlogId();
		}
	}
	
	/**
	 * @param integer $maxLength
	 * @return string
	 */
	public function getShortSummary($maxLength = 80)
	{
		$summary = ($this->getSummary()) ? $this->getSummaryAsHtml() : $this->getContentsAsHtml();
		return f_util_StringUtils::shortenString(f_util_StringUtils::htmlToText($summary), $maxLength);
	}
}