<?php
/**
 * blog_persistentdocument_category
 * @package blog.persistentdocument
 */
class blog_persistentdocument_category extends blog_persistentdocument_categorybase
{
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
}