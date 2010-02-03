<?php
/**
 * blog_PreferencesScriptDocumentElement
 * @package modules.blog.persistentdocument.import
 */
class blog_PreferencesScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return blog_persistentdocument_preferences
     */
    protected function initPersistentDocument()
    {
    	$document = ModuleService::getInstance()->getPreferencesDocument('blog');
    	return ($document !== null) ? $document : blog_PreferencesService::getInstance()->getNewDocumentInstance();
    }
}