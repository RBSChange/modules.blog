<?php
/**
 * blog_persistentdocument_keywordfolder
 * @package blog.persistentdocument
 */
class blog_persistentdocument_keywordfolder extends blog_persistentdocument_keywordfolderbase 
{	
	/**
	 * @return blog_persistentodcument_blog
	 */
	public function getBlog()
	{
		return $this->getDocumentService()->getParentOf($this);
	}
}