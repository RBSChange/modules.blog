<?php
/**
 * blog_PostfolderScriptDocumentElement
 * @package modules.blog.persistentdocument.import
 */
class blog_PostfolderScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return blog_persistentdocument_postfolder
     */
    protected function initPersistentDocument()
    {
    	return blog_PostfolderService::getInstance()->getNewDocumentInstance();
    }
}