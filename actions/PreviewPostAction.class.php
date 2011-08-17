<?php
/**
 * blog_PreviewAction
 * @package modules.blog.actions
 */
class blog_PreviewPostAction extends change_Action
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$document = $this->getDocumentInstanceFromRequest($request);
		$page = null;

		// retrieve the page to display
		if (!is_null($document) && $document instanceof blog_persistentdocument_post)
		{
			$document = DocumentHelper::getCorrection($document);
			$page = $document->getDocumentService()->getPreviewPage($document);
		}

		if (!is_null($page))
		{
			$model = $document->getPersistentModel();
			$request->setParameter($model->getModuleName()."Param", array("cmpref" => $document->getId()));

			
			//set pageref parameter into the request
			$request->setParameter('pageref', $page->getId());
			$module = 'website';
			$action = 'Display';
		}
		else
		{
			$module = 'website';
			$action = 'Error404';
		}

		// finally, forward the execution to $module / $action
		$context->getController()->forward($module, $action);
		return change_View::NONE;
	}

	/**
	 * @param change_Request $request
	 */
	protected function getDocumentIdArrayFromRequest($request)
	{
		$moduleName   = $this->getModuleName($request);
		$modulesParams = $request->getParameter($moduleName.'Param');
		$ids = $modulesParams[change_Request::DOCUMENT_ID];
		if (!is_array($ids))
		{
			$ids = explode(',', $ids);
		}
		return $ids;
	}

}