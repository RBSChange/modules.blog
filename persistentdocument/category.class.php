<?php
/**
 * blog_persistentdocument_category
 * @package blog.persistentdocument
 */
class blog_persistentdocument_category extends blog_persistentdocument_categorybase implements indexer_IndexableDocument
{
	/**
	 * Get the indexable document
	 * @return indexer_IndexedDocument
	 */
	public function getIndexedDocument()
	{
		$indexedDoc = new indexer_IndexedDocument();
		$indexedDoc->setId($this->getId());
		$indexedDoc->setDocumentModel('modules_blog/category');
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
	 * @return blog_persistentdocument_category[]
	 */
	public function getPublishedSubCategories()
	{
		return $this->getDocumentService()->getPublishedSubCategories($this);
	}
	
	/**
	 * @return String
	 */
	public function getLabelForUrl()
	{
		$labels = array();
		foreach ($this->getDocumentService()->getPublishedAncestorCategories($this) as $category)
		{
			$labels[] = $category->getLabel();
		}
		$labels[] = $this->getLabel();
		$labels = array_reverse($labels);
		return implode('-', $labels);
	}
	
	/**
	 * @return f_persistentdocument_PersistentDocument[]
	 */
	public function getBOChildren()
	{
		$query = blog_PostService::getInstance()->createQuery()->add(Restrictions::eq('category', $this));
		if (f_persistentdocument_PersistentDocumentModel::getInstance("blog", "post")->useCorrection())
		{
			$query->add(Restrictions::isNull('correctionofid'));
		}
		$query->addOrder(Order::desc('postdate'));
		return $query->find();
	}
}