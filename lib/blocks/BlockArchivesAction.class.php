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
	 * @return array<String, String>
	 */
	public function getMetas()
	{
		$blog = $this->getDocumentParameter();
		if ($blog !== null && $blog->isPublished())
		{
			$website = website_WebsiteService::getInstance()->getCurrentWebsite();
			$year = $this->findParameterValue('year');
			$month = $this->findParameterValue('month');
			$dates = blog_BlogService::getInstance()->getArchiveDates($year, $month);
			return array(
				'date' => $dates['startLabel'],
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
			return website_BlockView::NONE;
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
			$request->getParameter(paginator_Paginator::PAGEINDEX_PARAMETER_NAME, 1),
			$bs->getSortedPosts($blog, $dates),
			$this->getNbItemPerPage($request, $response)
		);
		$request->setAttribute('paginator', $paginator);
		
		// Add the RSS feed.
		$this->getContext()->addRssFeed($blog->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $blog->getId())));
				
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