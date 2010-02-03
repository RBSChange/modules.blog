<?php
/**
 * blog_KeywordScriptDocumentElement
 * @package modules.blog.persistentdocument.import
 */
class blog_KeywordScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return blog_persistentdocument_keyword
     */
    protected function initPersistentDocument()
    {
    	return blog_KeywordService::getInstance()->getNewDocumentInstance();
    }
}