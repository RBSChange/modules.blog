<?php
/**
 * blog_BlockArchivesAction
 * @package modules.blog.lib.blocks
 */
class blog_BlockArchivesAction extends website_TaggerBlockAction
{
    	/**
	 * @return String
	 */
	protected function getTag()
	{
		return "functional_blog_archives";
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
		if ($this->isInBackoffice())
		{
			return website_BlockView::BACKOFFICE;
		}

		$bs = blog_BlogService::getInstance();
		$blog = $this->getDocumentParameter();
		if ($blog === null || !$blog->isPublished())
		{
			return website_BlockView::NONE;
		}
		$request->setAttribute('blog', $blog);
		
		$year = $this->findParameterValue('year');
		$month = $this->findParameterValue('month');
		$dates = $bs->getArchiveDates($year, $month);
		$request->setAttribute('dates', $dates);
		
		// Set the paginator.
		$paginator = new paginator_Paginator('blog', 
			$request->getParameter(paginator_Paginator::REQUEST_PARAMETER_NAME, 1),
			$bs->getSortedPosts($blog, $dates),
			$this->getNbItemPerPage($request, $response)
		);
		$request->setAttribute('paginator', $paginator);
		
		// Set meta.
		$page = $this->getPage();
		$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
		$replacements = array(
			'date' => $dates['startLabel'],
			'blogLabel' => $blog->getLabel(),
			'siteLabel' => $website->getLabel()
		);
		$page->setMetatitle(f_Locale::translate('&modules.blog.frontoffice.Archives-meta-title;', $replacements));
		$page->appendToDescription(f_Locale::translate('&modules.blog.frontoffice.Archives-meta-description;', $replacements));
		
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