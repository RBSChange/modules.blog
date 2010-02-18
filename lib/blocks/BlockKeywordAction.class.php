<?php
/**
 * blog_BlockKeywordAction
 * @package modules.blog.lib.blocks.
 */
class blog_BlockKeywordAction extends website_BlockAction
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
				
		$keyword = $this->getDocumentParameter();
		if ($keyword === null || !$keyword->isPublished())
		{
			return website_BlockView::NONE;
		}
		$request->setAttribute('keyword', $keyword);
		
		// Set the paginator
		$paginator = new paginator_Paginator('blog', 
				$request->getParameter(paginator_Paginator::REQUEST_PARAMETER_NAME, 1),
				blog_KeywordService::getInstance()->getSortedPosts($keyword),
				$this->getNbItemPerPage($request, $response));

		$request->setAttribute('paginator', $paginator);
		
		// Set meta.
		$page = $this->getPage();
		$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
		$blog = $keyword->getBlog();
		$replacements = array(
			'keywordLabel' => $keyword->getLabel(),
			'keywordDescription' => $keyword->getDescription(),
			'blogLabel' => $blog->getLabel(),
			'siteLabel' => $website->getLabel()
		);
		$page->setMetatitle(f_Locale::translate('&modules.blog.frontoffice.Keyword-meta-title;', $replacements));
		$page->appendToDescription(f_Locale::translate('&modules.blog.frontoffice.Keyword-meta-description;', $replacements));
		
		// Add the RSS feeds.
		$page->addRssFeed($blog->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $blog->getId())));
		$page->addRssFeed($blog->getLabel() . ' : ' . $keyword->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $keyword->getId())));
		
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