<?php
/**
 * blog_BlockBlogAction
 * @package modules.blog.lib.blocks.lib.blocks
 */
class blog_BlockBlogAction extends website_BlockAction
{
	/**
	 * @see website_BlockAction::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function execute($request, $response)
	{
		if ($this->isInBackoffice())
		{
			return website_BlockView::BACKOFFICE;
		}
				
		$blog = $this->getDocumentParameter();
		if ($blog === null || !$blog->isPublished())
		{
			return website_BlockView::NONE;
		}
		$request->setAttribute('blog', $blog);
		
		// Set the paginator
		$paginator = new paginator_Paginator('blog', 
				$request->getParameter(paginator_Paginator::REQUEST_PARAMETER_NAME, 1),
				blog_BlogService::getInstance()->getSortedPosts($blog),
				$this->getNbItemPerPage($request, $response));

		$request->setAttribute('paginator', $paginator);
		
		// Set meta.
		$page = $this->getPage();
		$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
		$replacements = array(
			'blogLabel' => $blog->getLabel(),
			'blogDescription' => $blog->getDescription(),
			'siteLabel' => $website->getLabel()
		);
		$page->setMetatitle(f_Locale::translate('&modules.blog.frontoffice.Blog-meta-title;', $replacements));
		$page->appendToDescription(f_Locale::translate('&modules.blog.frontoffice.Blog-meta-description;', $replacements));
		
		// Add the RSS feed.
		$page->addRssFeed($blog->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $blog->getId())));
				
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