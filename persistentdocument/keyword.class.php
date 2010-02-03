<?php
/**
 * blog_persistentdocument_keyword
 * @package blog.persistentdocument
 */
class blog_persistentdocument_keyword extends blog_persistentdocument_keywordbase implements indexer_IndexableDocument
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
		$indexedDoc->setDocumentModel('modules_blog/keyword');
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
		return f_util_StringUtils::htmlToText($fullText);
	}
		
	/**
	 * @param string $moduleName
	 * @param string $treeType
	 * @param array<string, string> $nodeAttributes
	 */
	protected function addTreeAttributes($moduleName, $treeType, &$nodeAttributes)
	{
		$nodeAttributes['postCount'] = $this->getPostCount();
		$nodeAttributes['publishedPostCount'] = $this->getPublishedPostCount();
	}
}