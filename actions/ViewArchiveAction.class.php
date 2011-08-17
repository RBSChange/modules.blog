<?php
/**
 * blog_ViewArchiveAction
 * @package modules.blog.actions
 */
class blog_ViewArchiveAction extends change_Action
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$module = 'website';
		$action = 'Error404';

		$documentId = $request->getModuleParameter('blog', 'cmpref');
		if (null === $documentId)
		{
			$documentId = $request->getParameter('cmpref');
		}		
		
		$document = DocumentHelper::getDocumentInstance($documentId);
		if ($document !== null)
		{
			// Find archive page for the document to display.
			$page = $this->getArchivePage($document);
			if ($page !== null)
			{
				$request->setParameter('pageref', $page->getId());
				$module = 'website';
				$action = 'Display';
			}
		}

		$context->getController()->forward($module, $action);
		return change_View::NONE;
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
	
	/**
	 * @return boolean
	 */
	public function isSecure()
	{
		return false;
	}
}