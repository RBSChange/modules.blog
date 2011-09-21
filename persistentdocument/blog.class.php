<?php
/**
 * blog_persistentdocument_blog
 * @package blog.persistentdocument
 */
class blog_persistentdocument_blog extends blog_persistentdocument_blogbase implements indexer_IndexableDocument
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
		$indexedDoc->setDocumentModel('modules_blog/blog');
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
		$fullText = $this->getDescriptionAsHtml();
		$fullText .= ' ' . implode(', ', $this->getAuthorNames());
		return f_util_StringUtils::htmlToText($fullText);
	}
	
	/**
	 * @return blog_persistentdocument_category[]
	 */
	public function getPublishedFistLevelCategories()
	{
		return blog_CategoryService::getInstance()->getPublishedFistLevelByBlog($this);
	}
	
	/**
	 * @return Integer
	 */
	public function getPublishedPostCount()
	{
		return $this->getDocumentService()->getPublishedPostCount($this);
	}
	
	/**
	 * @return blog_persistentdocument_post
	 */
	public function getLastPublishedPost()
	{
		return $this->getDocumentService()->getLastPublishedPost($this);
	}
	
	/**
	 * @return String[]
	 */
	public function getAuthorNames()
	{
		return $this->getDocumentService()->getAuthorNames($this);
	}
	
	/**
	 * @param string $order 'desc'|'asc'
	 * @return blog_persistentdocument_year[]
	 */
	public function getPublishedYears($order = 'desc')
	{
		return blog_YearService::getInstance()->getPublishedByBlog($this, $order);
	}
	
	/**
	 * @return String[]
	 */
	public function getPingurlsArray()
	{
		if (f_util_StringUtils::isEmpty($this->getPingurls()))
		{
			return array();
		}
		return explode(",", $this->getPingurls());
	}
	
	/**
	 * @param String[] $urls
	 */
	public function setPingurlsArray($urls)
	{
		$this->setPingurls(implode(",", $urls));
	}
}