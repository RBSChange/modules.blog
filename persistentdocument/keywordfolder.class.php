<?php
/**
 * blog_persistentdocument_keywordfolder
 * @package blog.persistentdocument
 */
class blog_persistentdocument_keywordfolder extends blog_persistentdocument_keywordfolderbase 
{
	/**
	 * @return f_persistentdocument_PersistentDocument[]
	 */
	public function getBOChildren()
	{
		$blogId = $this->getDocumentService()->getParentOf($this)->getId();
		return blog_KeywordService::getInstance()->createQuery()->add(Restrictions::eq('blog.id', $blogId))->find();
	}
}