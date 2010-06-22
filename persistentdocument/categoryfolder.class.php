<?php
/**
 * blog_persistentdocument_categoryfolder
 * @package blog.persistentdocument
 */
class blog_persistentdocument_categoryfolder extends blog_persistentdocument_categoryfolderbase
{
	/**
	 * @return blog_persistentodcument_blog
	 */
	public function getBlog()
	{
		return $this->getDocumentService()->getParentOf($this);
	}
}