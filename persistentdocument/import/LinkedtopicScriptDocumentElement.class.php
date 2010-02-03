<?php
class blog_LinkedtopicScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return website_persistentdocument_topic
     */
    protected function initPersistentDocument()
    {
        $refid = $this->attributes['refid'];
        return $this->script->getElementById($refid)->getPersistentDocument();
    }
    
    public function process()
    {
        $topic = $this->getPersistentDocument();
        $rootFolder = $this->getParentDocument()->getPersistentDocument();
        $rootFolder->addTopics($topic);
        $rootFolder->save();
    }
}