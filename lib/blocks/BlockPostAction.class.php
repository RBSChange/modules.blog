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

	function getCacheKeyParameters($request)
	{
		return array('cmpref' => $this->getDocumentIdParameter(), 'context->lang' => $this->getLang(), 'pageId' => $this->getPage()->getId());
	}
	
	function getCacheDependencies()
	{
		return array("modules_blog/blog", "modules_blog/post", "modules_blog/category", "modules_media/media", "modules_website/page");
	}
	
	function initialize($request, $response)
	{
		$post = $this->getDocumentParameter();
		if ($post === null)
		{
			return;
		}
		if ($post instanceof blog_persistentdocument_post && $post->getAllowPingbacks() == true)
		{
			header('X-Pingback: ' . LinkHelper::getActionUrl('blog', 'PingBack', array('postId' => $post->getId())));
		}
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
		$this->getPage()->addRssFeed($blog->getLabel(), LinkHelper::getActionUrl('blog', 'ViewFeed', array('parentref' => $blog->getId())));
		
		return website_BlockView::SUCCESS;
	}
	
	function getMetas()
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
		return array("postLabel" => $post->getLabel(), "postDate" => date_DateFormat::format($post->getPostDate(), 'd/m/Y'), "postSummary" => f_util_StringUtils::htmlToText($post->getSummary(), false, true), "blogLabel" => $blog->getLabel(), "blogDescription" => f_util_StringUtils::htmlToText($blog->getDescription(), false, true), "postKeywords" => join(",", $postKeywords), "postCategories" => join(",", $postCategories));
	}
	
	/**
	 * @return Boolean
	 */
	private function getShowShareBlock()
	{
		return ModuleService::getInstance()->isInstalled('modules_sharethis') && $this->getConfiguration()->getShowshareblock();
	}
}
