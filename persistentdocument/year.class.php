<?php
/**
 * blog_persistentdocument_year
 * @package blog.persistentdocument
 */
class blog_persistentdocument_year extends blog_persistentdocument_yearbase 
{
	/**
	 * @param string $order 'desc'|'asc'
	 * @return blog_persistentdocument_year[]
	 */
	public function getPublishedMonths($order = 'desc')
	{
		return blog_MonthService::getInstance()->getPublishedByYear($this, $order);
	}
}