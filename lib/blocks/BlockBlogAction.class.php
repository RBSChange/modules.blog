<?php
/**
 * blog_BlockBlogAction
 * @package modules.blog.lib.blocks.lib.blocks
 */
class blog_BlockBlogAction extends website_TaggerBlockAction
{
	/**
	 * @return String
	 */
	protected function getTag()
	{
		return "functional_blog_blog-detail";
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function execute($request, $response)
	{
		$blog = $this->getDocumentParameter();
		if ($blog === null || !$blog->isPublished())
		{
			return website_BlockView::NONE;
		}
		$request->setAttribute('blog', $blog);
		
		// Set the paginator
		$paginator = new paginator_Paginator('blog',
			$request->getParameter(paginator_Paginator::PAGEINDEX_PARAMETER_NAME, 1),
			blog_BlogService::getInstance()->getSortedPosts($blog),
			$this->getNbItemPerPage($request, $response)
		);
		
		$request->setAttribute('paginator', $paginator);
		
		// Add the RSS feed.
		$page = $this->getContext();
		$page->addRssFeed($blog->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $blog->getId())));
		
		return website_BlockView::SUCCESS;
	}
	
	/**
	 * @return array
	 */
	public function getMetas()
	{
		$blog = $this->getDocumentParameter();
		if ($blog === null || !$blog->isPublished())
		{
			return array();
		}
		return array('blogLabel' => $blog->getLabel(), 'blogDescription' => $blog->getDescription());
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