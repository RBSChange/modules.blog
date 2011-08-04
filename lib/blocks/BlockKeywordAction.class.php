<?php
/**
 * blog_BlockKeywordAction
 * @package modules.blog.lib.blocks.
 */
class blog_BlockKeywordAction extends website_TaggerBlockAction
{
    /**
	 * @return String
	 */
	protected function getTag()
	{
		return "functional_blog_keyword-detail";
	}
	
	/**
	 * @return array<String, String>
	 */
	public function getMetas()
	{
		$keyword = $this->getDocumentParameter();
		if ($keyword !== null && $keyword->isPublished())
		{
			$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
			$blog = $keyword->getBlog();
			return array(
				'keywordLabel' => $keyword->getLabel(),
				'keywordDescription' => $keyword->getDescription(),
				'blogLabel' => $blog->getLabel(),
				'siteLabel' => $website->getLabel()
			);
		}
		return array();
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function execute($request, $response)
	{
		if ($this->isInBackofficeEdition())
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
			$request->getParameter(paginator_Paginator::PAGEINDEX_PARAMETER_NAME, 1),
			blog_KeywordService::getInstance()->getSortedPosts($keyword),
			$this->getNbItemPerPage($request, $response)
		);

		$request->setAttribute('paginator', $paginator);

		// Add the RSS feeds.
		$blog = $keyword->getBlog();
		$context = $this->getContext();
		$context->addRssFeed($blog->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $blog->getId())));
		$context->addRssFeed($blog->getLabel() . ' : ' . $keyword->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $keyword->getId())));
		
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