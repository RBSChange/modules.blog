<?php
/**
 * blog_BlockLastPostsAction
 * @package modules.blog.lib.blocks
 */
class blog_BlockLastPostsAction extends website_BlockAction
{
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function execute($request, $response)
	{
		if ($this->isInBackoffice())
		{
			return website_BlockView::NONE;
		}
		
		$configuration = $this->getConfiguration();
		$posts = blog_PostService::getInstance()->getLastPublished($configuration->getBlog(), $configuration->getMaxCount());
		$request->setAttribute('posts', $posts);
		
		return website_BlockView::SUCCESS;
	}
}