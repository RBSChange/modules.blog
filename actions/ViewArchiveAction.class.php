<?php
/**
 * blog_ViewArchiveAction
 * @package modules.blog.actions
 */
class blog_ViewArchiveAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$module = AG_ERROR_404_MODULE;
		$action = AG_ERROR_404_ACTION;

		$documentId = $request->getModuleParameter('blog', K::COMPONENT_ID_ACCESSOR);
		if (null === $documentId)
		{
			$documentId = $request->getParameter(K::COMPONENT_ID_ACCESSOR);
		}		
		
		$document = DocumentHelper::getDocumentInstance($documentId);
		if ($document !== null)
		{
			// Find archive page for the document to display.
			$page = $this->getArchivePage($document);
			if ($page !== null)
			{
				$request->setParameter(K::PAGE_REF_ACCESSOR, $page->getId());
				$module = 'website';
				$action = 'Display';
			}
		}

		$context->getController()->forward($module, $action);
		return View::NONE;
	}
	
	/**
	 * @param blog_persistentdocument_blog $document
	 * @return website_persistentdocument_page
	 */
	private function getArchivePage($document)
	{
		try
		{
			$page = TagService::getInstance()->getDocumentBySiblingTag(
				'functional_blog_archives',
				$document
			);
		}
		catch (TagException $e)
		{			
			$page = null;
			//No taged Page found
			Framework::exception($e);
		}

		return $page;
	}
	
	/* (non-PHPdoc)
     * @see f_action_BaseAction::isSecure()
     */
    public function isSecure ()
    {
        return false;
    }
}