<?php
/**
 * blog_CategoryfolderScriptDocumentElement
 * @package modules.blog.persistentdocument.import
 */
class blog_CategoryfolderScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return blog_persistentdocument_categoryfolder
     */
    protected function initPersistentDocument()
    {
    	return blog_CategoryfolderService::getInstance()->getNewDocumentInstance();
    }
}