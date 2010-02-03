<?php
/**
 * blog_BlockCategoryAction
 * @package modules.blog.lib.blocks.lib.blocks
 */
class blog_BlockCategoryAction extends website_BlockAction
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
				
		$category = $this->getDocumentParameter();
		if ($category === null || !$category->isPublished())
		{
			return website_BlockView::NONE;
		}
		$request->setAttribute('category', $category);
		
		// Set the paginator
		$paginator = new paginator_Paginator('blog', 
				$request->getParameter(paginator_Paginator::REQUEST_PARAMETER_NAME, 1),
				blog_CategoryService::getInstance()->getSortedPosts($category, $this->getConfiguration()->getIncludedescendents()),
				$this->getNbItemPerPage($request, $response)
		);
				
		$request->setAttribute('paginator', $paginator);
		
		// Set meta.
		$page = $this->getPage();
		$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
		$blog = $category->getBlog();
		$replacements = array(
			'categoryLabel' => $category->getLabel(),
			'categoryDescription' => $category->getDescription(),
			'blogLabel' => $blog->getLabel(),
			'siteLabel' => $website->getLabel()
		);
		$page->setMetatitle(f_Locale::translate('&modules.blog.frontoffice.Category-meta-title;', $replacements));
		$page->appendToDescription(f_Locale::translate('&modules.blog.frontoffice.Category-meta-description;', $replacements));
		
		// Add the RSS feeds.
		$page->addRssFeed($blog->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $blog->getId())));
		$page->addRssFeed($blog->getLabel() . ' : ' . $category->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $category->getId())));
		
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