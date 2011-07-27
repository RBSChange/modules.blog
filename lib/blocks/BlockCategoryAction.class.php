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
	 * @see website_BlockAction::execute()
	 *
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
		
		// Set meta.
		$context = $this->getContext();
		$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
		$blog = $category->getBlog();
		$replacements = array(
			'categoryLabel' => $category->getLabel(),
			'categoryDescription' => $category->getDescription(),
			'blogLabel' => $blog->getLabel(),
			'siteLabel' => $website->getLabel()
		);
		$context->setMetatitle(f_Locale::translate('&modules.blog.frontoffice.Category-meta-title;', $replacements));
		$context->appendToDescription(f_Locale::translate('&modules.blog.frontoffice.Category-meta-description;', $replacements));
		
		// Add the RSS feeds.
		$context->addRssFeed($blog->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $blog->getId())));
		$context->addRssFeed($blog->getLabel() . ' : ' . $category->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $category->getId())));
		
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