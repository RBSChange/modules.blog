<?php
/**
 * blog_persistentdocument_blog
 * @package blog.persistentdocument
 */
class blog_persistentdocument_blog extends blog_persistentdocument_blogbase
{
	
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
	 * @return blog_persistentdocument_year[]
	 */
	public function getPublishedYears()
	{
		return blog_YearService::getInstance()->getPublishedByBlog($this);
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