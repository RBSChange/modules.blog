<?php
/**
 * blog_PreviewAction
 * @package modules.blog.actions
 */
class blog_PreviewPostAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
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
			$rc = RequestContext::getInstance();
			$lang = $page->getLang();
			if (!$page->getPublicationstatusForLang($page->getLang()))
			{
				$lang = $rc->getLang();
			}
			$rc->setLang($lang);
			website_WebsiteModuleService::getInstance()->setCurrentWebsiteId($page->getDocumentService()->getWebsiteId($page));
			
			$model = $document->getPersistentModel();
			$request->setParameter($model->getOriginalModuleName()."Param", array("cmpref" => $document->getId()));

			
			//set pageref parameter into the request
			$request->setParameter(K::PAGE_REF_ACCESSOR, $page->getId());
			$module = 'website';
			$action = 'Display';
		}
		else
		{
			$module = AG_ERROR_404_MODULE;
			$action = AG_ERROR_404_ACTION;
		}

		// finally, forward the execution to $module / $action
		$context->getController()->forward($module, $action);
		return View::NONE;
	}

	/**
	 * @param Request $request
	 */
	protected function getDocumentIdArrayFromRequest($request)
	{
		$moduleName   = $this->getModuleName($request);
		$modulesParams = $request->getParameter($moduleName.'Param');
		$ids = $modulesParams[K::COMPONENT_ID_ACCESSOR];
		if (!is_array($ids))
		{
			$ids = explode(',', $ids);
		}
		return $ids;
	}

}