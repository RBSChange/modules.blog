<?php
/**
 * @author intportg
 * @package modules.blog.action
 */
class blog_ViewDetailAction extends generic_ViewDetailAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$module = AG_ERROR_404_MODULE;
		$action = AG_ERROR_404_ACTION;

		$document = $this->getDocumentInstanceFromRequest($request);
		if ($document !== null)
		{
			// Find detail page for the document to display.
			$page = $this->getDetailPage($document);
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
	 * @param f_persistentdocument_PersistentDocumentImpl $document
	 * @return website_persistentdocument_page
	 */
	private function getDetailPage($document)
	{
		if ($document instanceof blog_persistentdocument_post || $document instanceof blog_persistentdocument_category || $document instanceof blog_persistentdocument_keyword)
		{
			$treeAncestor = $document->getBlog();
		}
		else
		{
			$treeAncestor = $document;
		}
		
		$page = null;
		try
		{
			$page = TagService::getInstance()->getDocumentBySiblingTag(
				$this->getFunctionalTag($document),
				$treeAncestor
			);
		}
		catch (TagException $e)
		{
			//No taged Page found
			Framework::exception($e);
		}

		return $page;
	}

	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @return String
	 */
	private function getFunctionalTag($document)
	{
		$model = $document->getPersistentModel();
		if (!is_null($sourceModel = $model->getSourceInjectionModel()))
		{
			$model = $sourceModel;
		}
		return 'functional_' . $model->getModuleName() . '_' . $model->getDocumentName() .'-detail';
	}
}