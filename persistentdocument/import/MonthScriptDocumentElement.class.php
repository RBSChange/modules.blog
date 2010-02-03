<?php
/**
 * blog_MonthScriptDocumentElement
 * @package modules.blog.persistentdocument.import
 */
class blog_MonthScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return blog_persistentdocument_month
     */
    protected function initPersistentDocument()
    {
    	return blog_MonthService::getInstance()->getNewDocumentInstance();
    }
}