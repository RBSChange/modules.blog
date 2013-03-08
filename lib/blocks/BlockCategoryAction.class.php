<?php
/**
 * blog_BlockCategoryAction
 * @package modules.blog.lib.blocks.lib.blocks
 */
class blog_BlockCategoryAction extends website_TaggerBlockAction
{
	/**
	 * @return String
	 */
	protected function getTag()
	{
		return "functional_blog_category-detail";
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function execute($request, $response)
	{
		$category = $this->getDocumentParameter();
		if ($category === null || !$category->isPublished())
		{
			return website_BlockView::NONE;
		}
		$request->setAttribute('category', $category);
		
		// Set the paginator
		$paginator = new paginator_Paginator('blog',
			$request->getParameter(paginator_Paginator::PAGEINDEX_PARAMETER_NAME, 1),
			blog_CategoryService::getInstance()->getSortedPosts($category, $this->getConfiguration()->getIncludedescendents()),
			$this->getNbItemPerPage($request, $response)
		);
		
		$request->setAttribute('paginator', $paginator);
		
		// Add the RSS feeds.
		$page = $this->getPage();
		$blog = $category->getBlog();
		$page->addRssFeed($blog->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $blog->getId())));
		$page->addRssFeed($blog->getLabel() . ' : ' . $category->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $category->getId())));
		
		return website_BlockView::SUCCESS;
	}
	
	/**
	 * @return array
	 */
	public function getMetas()
	{
		$category = $this->getDocumentParameter();
		if ($category === null || !$category->isPublished())
		{
			return array();
		}
		$blog = $category->getBlog();
		return array('categoryLabel' => $category->getLabel(), 'categoryDescription' => $category->getDescription(), 'blogLabel' => $blog->getLabel());
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