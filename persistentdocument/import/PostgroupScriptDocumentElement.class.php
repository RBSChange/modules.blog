<?php
/**
 * blog_PostgroupScriptDocumentElement
 * @package modules.blog.persistentdocument.import
 */
class blog_PostgroupScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return blog_persistentdocument_postgroup
     */
    protected function initPersistentDocument()
    {
    	return blog_PostgroupService::getInstance()->getNewDocumentInstance();
    }
}