<?php
/**
 * blog_PingbackScriptDocumentElement
 * @package modules.blog.persistentdocument.import
 */
class blog_PingbackScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return blog_persistentdocument_pingback
     */
    protected function initPersistentDocument()
    {
    	return blog_PingbackService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_blog/pingback');
	}
}