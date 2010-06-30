<?php
/**
 * blog_persistentdocument_postfolder
 * @package blog.persistentdocument
 */
class blog_persistentdocument_postfolder extends blog_persistentdocument_postfolderbase 
{	
	/**
	 * @return blog_persistentodcument_blog
	 */
	public function getBlog()
	{
		return $this->getDocumentService()->getParentOf($this);
	}
}