<?php
/**
 * @author michal.olexa
 * @package modules.blog
 */
class blog_BlockBlogEditAction extends website_BlockAction
{
	/**
	 * @see f_mvc_Action::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function execute($request, $response)
	{
		if ($this->isInBackoffice())
		{
			return website_BlockView::NONE;
		}
		
		$blog = $this->getDocumentParameter();
		if (!$blog)
		{
			// Set the paginator.
			$pageIndex = $request->getParameter(paginator_Paginator::PAGEINDEX_PARAMETER_NAME, 1);
			$paginator = new paginator_Paginator('blog', $pageIndex, $this->getDocumentList($request, $response), $this->getNbItemPerPage($request, $response));
			$request->setAttribute('paginator', $paginator);
			return 'List';
		}
		else if (!$blog->getDocumentService()->checkAdminPermissionsForCurrentFrontendUser($blog))
		{
			$this->addError(f_Locale::translate('&modules.blog.frontoffice.Error-no-permission-on-blog;'));
			return website_BlockView::ERROR;
		}
		$request->setAttribute('blog', $blog);
		return $this->getInputViewName();
	}
	
	/**
	 * @see f_mvc_Action::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function executeDelete($request, $response)
	{
		$document = DocumentHelper::getDocumentInstance($this->findParameterValue('documentId'));
		
		$blog = $document->getBlog();
		if (!$blog->getDocumentService()->checkAdminPermissionsForCurrentFrontendUser($blog))
		{
			$this->addError(f_Locale::translate('&modules.blog.frontoffice.Error-no-permission-on-blog;'));
			return website_BlockView::ERROR;
		}
		
		$document->delete();
		
		$request->setAttribute('blog', $blog);
		return $this->getInputViewName();
	}
	
	/**
	 * @see f_mvc_Action::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function executeActivate($request, $response)
	{
		$document = DocumentHelper::getDocumentInstance($this->findParameterValue('documentId'));
		
		$blog = $document->getBlog();
		if (!$blog->getDocumentService()->checkAdminPermissionsForCurrentFrontendUser($blog))
		{
			$this->addError(f_Locale::translate('&modules.blog.frontoffice.Error-no-permission-on-blog;'));
			return website_BlockView::ERROR;
		}
		
		$document->activate();
		
		$request->setAttribute('blog', $blog);
		return $this->getInputViewName();
	}
	
	/**
	 * @see f_mvc_Action::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function executeDeactivate($request, $response)
	{
		$document = DocumentHelper::getDocumentInstance($this->findParameterValue('documentId'));
		
		$blog = $document->getBlog();
		if (!$blog->getDocumentService()->checkAdminPermissionsForCurrentFrontendUser($blog))
		{
			$this->addError(f_Locale::translate('&modules.blog.frontoffice.Error-no-permission-on-blog;'));
			return website_BlockView::ERROR;
		}
		
		$document->deactivate();
		
		$request->setAttribute('blog', $blog);
		return $this->getInputViewName();
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return Integer default 10
	 */
	private function getNbItemPerPage($request, $response)
	{
		return $this->getConfiguration()->getNbitemperpage();
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return f_persistentdocument_PersistentDocument[]
	 */
	private function getDocumentList($request, $response)
	{
		// Get all blogs in the current website.
		$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
		$blogs = blog_BlogService::getInstance()->getByWebsite($website);
		
		// Filter blogs by permissions.
		foreach ($blogs as $index => $blog)
		{
			if (!$blog->getDocumentService()->checkAdminPermissionsForCurrentFrontendUser($blog))
			{
				unset($blogs[$index]);
			}
		}
		
		return $blogs;
	}
}