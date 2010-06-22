<?php
/**
 * blog_persistentdocument_postfolder
 * @package blog.persistentdocument
 */
class blog_persistentdocument_postfolder extends blog_persistentdocument_postfolderbase 
{
	/**
	 * @return f_persistentdocument_PersistentDocument[]
	 */
	public function getBOChildren()
	{
		$blogId = $this->getDocumentService()->getParentOf($this)->getId();
		$query = blog_PostService::getInstance()->createQuery()->add(Restrictions::eq('blog.id', $blogId));
		if (f_persistentdocument_PersistentDocumentModel::getInstance("blog", "post")->useCorrection())
		{
			$query->add(Restrictions::isNull('correctionofid'));
		}
		$query->addOrder(Order::desc('postdate'));
		return $query->find();
	}
	
	/**
	 * @return blog_persistentodcument_blog
	 */
	public function getBlog()
	{
		return $this->getDocumentService()->getParentOf($this);
	}
}