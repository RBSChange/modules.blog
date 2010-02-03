<?php
/**
 * blog_BlogScriptDocumentElement
 * @package modules.blog.persistentdocument.import
 */
class blog_BlogScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return blog_persistentdocument_blog
     */
    protected function initPersistentDocument()
    {
    	return blog_BlogService::getInstance()->getNewDocumentInstance();
    }
}