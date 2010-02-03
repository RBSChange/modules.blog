<?php
/**
 * blog_KeywordfolderScriptDocumentElement
 * @package modules.blog.persistentdocument.import
 */
class blog_KeywordfolderScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return blog_persistentdocument_keywordfolder
     */
    protected function initPersistentDocument()
    {
    	return blog_KeywordfolderService::getInstance()->getNewDocumentInstance();
    }
}