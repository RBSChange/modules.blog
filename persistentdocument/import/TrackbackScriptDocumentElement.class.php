<?php
/**
 * blog_TrackbackScriptDocumentElement
 * @package modules.blog.persistentdocument.import
 */
class blog_TrackbackScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return blog_persistentdocument_trackback
     */
    protected function initPersistentDocument()
    {
    	return blog_TrackbackService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_blog/trackback');
	}
}