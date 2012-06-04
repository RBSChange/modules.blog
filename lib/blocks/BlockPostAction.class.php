<?php
/**
 * blog_BlockPostAction
 * @package modules.blog.lib.blocks.lib.blocks
 */
class blog_BlockPostAction extends website_TaggerBlockAction
{
    /**
	 * @return String
	 */
	protected function getTag()
	{
		return "functional_blog_post-detail";
	}
	
	/**
	 * @return array<String, String>
	 */
	public function getMetas()
	{
		$post = $this->getDocumentParameter();
		if ($post === null || !$post->isPublished())
		{
			return array();
		}
		$postKeywords = array();
		foreach ($post->getKeywordArray() as $keyword)
		{
			$postKeywords[] = $keyword->getLabel();
		}
		$postCategories = array();
		foreach ($post->getCategoryArray() as $category)
		{
			$postCategories[] = $category->getLabel();
		}
		$blog = $post->getBlog();
		$website = website_WebsiteService::getInstance()->getCurrentWebsite();
		return array(
			'postLabel' => $post->getLabel(),
			'postDate' => date_Formatter::toDefaultDate($post->getPostDate()),
			'postSummary' => f_util_HtmlUtils::htmlToText($post->getSummary(), false, true),
			'blogLabel' => $blog->getLabel(),
			'blogDescription' => f_util_HtmlUtils::htmlToText($blog->getDescription(), false, true),
			'postKeywords' => join(',', $postKeywords),
			'postCategories' => join(',', $postCategories),
			'siteLabel' => $website->getLabel()
		);
	}
	
	/**
	 * @return array
	 */
	public function getCacheDependencies()
	{
		return array($this->getDocumentIdParameter());
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 */
	public function initialize($request, $response)
	{
		if ($this->isInBackoffice())
		{
			return;
		}
		
		$post = $this->getDocumentParameter();
		if ($post instanceof blog_persistentdocument_post && $post->getAllowPingbacks() == true)
		{
			header('X-Pingback: ' . LinkHelper::getActionUrl('blog', 'PingBack', array('postId' => $post->getId())));
		}
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function execute($request, $response)
	{
		$post = $this->getDocumentParameter();
		if ($post === null)
		{
			return website_BlockView::NONE;
		}
		if ($post instanceof blog_persistentdocument_post && $post->getAllowPingbacks() == true)
		{
			$this->getContext()->addLink("pingback", "", LinkHelper::getActionUrl('blog', 'PingBack', array('postId' => $post->getId())));
		}
		$request->setAttribute('post', $post);
		
		// Include share block?
		$request->setAttribute('showShareBlock', $this->getShowShareBlock());
		
		// Trackback URL
		$request->setAttribute('trackbackurl', LinkHelper::getActionUrl('blog', 'TrackBack', array('lang' => $this->getLang(), 'cmpref' => $post->getId())));
		
		// Add the RSS feeds.
		$blog = $post->getBlog();
		$this->getContext()->addRssFeed($blog->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $blog->getId())));
		
		return website_BlockView::SUCCESS;
	}
	
	/**
	 * @return Boolean
	 */
	private function getShowShareBlock()
	{
		return ModuleService::getInstance()->isInstalled('modules_sharethis') && $this->getConfiguration()->getShowshareblock();
	}
}