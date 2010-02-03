<?php
/**
 * blog_CategoryScriptDocumentElement
 * @package modules.blog.persistentdocument.import
 */
class blog_CategoryScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return blog_persistentdocument_category
     */
    protected function initPersistentDocument()
    {
    	return blog_CategoryService::getInstance()->getNewDocumentInstance();
    }
    
	protected function saveDocument()
    {
        $document = $this->getPersistentDocument();
        $parent = $this->getParentDocument();
    	$parentDocument = $parent->getPersistentDocument();
    	if ($parentDocument instanceof blog_persistentdocument_blog)
    	{
    		$folder = blog_CategoryfolderService::getInstance()->getByBlog($parentDocument);
    		$parentId = $folder->getId();
    	}
    	else
    	{
    		$parentId = ($parent) ? $parent->getPersistentDocument()->getId() : null;
    	}
        $document->save($parentId);     
    }
}