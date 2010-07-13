<?php
/**
 * blog_persistentdocument_postgroup
 * @package blog.persistentdocument
 */
class blog_persistentdocument_postgroup extends blog_persistentdocument_postgroupbase implements indexer_IndexableDocument{
	/**
	 * Get the indexable document
	 *
	 * @return indexer_IndexedDocument
	 */
	public function getIndexedDocument()
	{
		$indexedDoc = new indexer_IndexedDocument();
		$indexedDoc->setId($this->getId());
		$indexedDoc->setDocumentModel($this->getDocumentModelName());
		$indexedDoc->setLabel($this->getLabel());
		$indexedDoc->setLang($this->getLang());
		$indexedDoc->setText($this->getFullTextForIndexation());
		return $indexedDoc;
	}
	
	/**
	 * @return String
	 */
	protected function getFullTextForIndexation()
	{
		$fullText = $this->getDescriptionAsHtml();
		return f_util_StringUtils::htmlToText($fullText);
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
}