<?php
/**
 * blog_persistentdocument_year
 * @package blog.persistentdocument
 */
class blog_persistentdocument_year extends blog_persistentdocument_yearbase 
{
	/**
	 * @return blog_persistentdocument_year[]
	 */
	public function getPublishedMonths()
	{
		return blog_MonthService::getInstance()->getPublishedByYear($this);
	}
}