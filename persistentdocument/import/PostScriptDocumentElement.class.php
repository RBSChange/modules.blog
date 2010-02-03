<?php
/**
 * blog_PostScriptDocumentElement
 * @package modules.blog.persistentdocument.import
 */
class blog_PostScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return blog_persistentdocument_post
     */
    protected function initPersistentDocument()
    {
        $post = blog_PostService::getInstance()->getNewDocumentInstance();

        $categories = array();
        foreach (explode(',', $this->attributes['categories']) as $categoryRef)
        {
        	$categories[] = $this->script->getElementById(trim($categoryRef))->getPersistentDocument();
        }
        unset($this->attributes['categories']);
        $post->setCategoryArray($categories);
        
        return $post;
    }
    
	public function endProcess()
    {
        $document = $this->getPersistentDocument();
        if ($document->getPublicationstatus() == 'DRAFT')
        {
            $document->activate();
        }
    }
}