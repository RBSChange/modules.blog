<?php
/**
 * blog_YearScriptDocumentElement
 * @package modules.blog.persistentdocument.import
 */
class blog_YearScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return blog_persistentdocument_year
     */
    protected function initPersistentDocument()
    {
    	return blog_YearService::getInstance()->getNewDocumentInstance();
    }
}