<?php
/**
 * blog_persistentdocument_postgroup
 * @package blog.persistentdocument
 */
class blog_persistentdocument_postgroup extends blog_persistentdocument_postgroupbase
{
	
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