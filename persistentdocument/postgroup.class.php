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
		// TODO : set the different properties you want in you indexedDocument :
		// - please verify that id, documentModel, label and lang are correct according your requirements
		// - please set text value.
		$indexedDoc->setId($this->getId());
		$indexedDoc->setDocumentModel('modules_blog/postgroup');
		$indexedDoc->setLabel($this->getLabel());
		$indexedDoc->setLang($this->getLang());
		$indexedDoc->setText(null); // TODO : please fill text property
		return $indexedDoc;
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