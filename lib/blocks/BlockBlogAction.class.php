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
	 * @see website_BlockAction::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function execute($request, $response)
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
				$this->getNbItemPerPage($request, $response));

		$request->setAttribute('paginator', $paginator);
		
		// Set meta.
		$context = $this->getContext();
		$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
		$replacements = array(
			'blogLabel' => $blog->getLabel(),
			'blogDescription' => $blog->getDescription(),
			'siteLabel' => $website->getLabel()
		);
		$context->setMetatitle(f_Locale::translate('&modules.blog.frontoffice.Blog-meta-title;', $replacements));
		$context->appendToDescription(f_Locale::translate('&modules.blog.frontoffice.Blog-meta-description;', $replacements));
		
		// Add the RSS feed.
		$context->addRssFeed($blog->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $blog->getId())));
				
		return website_BlockView::SUCCESS;
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