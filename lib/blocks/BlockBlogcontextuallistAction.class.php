<?php
/**
 * blog_BlockBlogcontextuallistAction
 * @package modules.blog.lib.blocks
 */
class blog_BlockBlogcontextuallistAction extends website_BlockAction
{
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function execute($request, $response)
	{
		// Set the paginator
		$paginator = new paginator_Paginator('blog', 
				$request->getParameter(paginator_Paginator::REQUEST_PARAMETER_NAME, 1),
				$this->getDocumentList($request, $response),
				$this->getNbItemPerPage($request, $response));
		$request->setAttribute('paginator', $paginator);
		
		return website_BlockView::SUCCESS;
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
		// Get the parent document instance
        $parent = $this->getPage()->getParent();
		$request->setAttribute('parent', $parent);
		return blog_BlogService::getInstance()->getByParentId($parent->getId());
	}
}