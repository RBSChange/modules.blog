<?php
class blog_BlogEditLoadHandler extends website_ViewLoadHandlerImpl
{
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 */
	function execute($request, $response)
	{
		$blog = $request->getAttribute('blog');

		$request->setAttribute('categories', $this->getCategories($blog));
			
		// Set the paginator
		$paginator = new paginator_Paginator('blog', 
				$request->getParameter(paginator_Paginator::REQUEST_PARAMETER_NAME, 1),
				$this->getPosts($blog),
				$this->getNbItemPerPage($request, $response));

		$request->setAttribute('paginator', $paginator);
	}
	
	private function getPosts($blog)
	{
		return blog_PostService::getInstance()->createQuery()
			->add(Restrictions::eq('blog', $blog->getId()))
			->addOrder(Order::desc('postdate'))
			->find();
	}
	
	private function getCategories($blog)
	{
		return blog_CategoryService::getInstance()->createStrictQuery()
			->add(Restrictions::eq('blog', $blog->getId()))
			->find();
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return Integer default 5
	 */
	private function getNbItemPerPage($request, $response)
	{
		return $this->getConfiguration()->getNbitemperpage();
	}	
}