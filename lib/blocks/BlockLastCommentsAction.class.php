<?php
/**
 * blog_BlockLastCommentsAction
 * @package modules.blog.lib.blocks
 */
class blog_BlockLastCommentsAction extends website_BlockAction
{
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function execute($request, $response)
	{
		if ($this->isInBackofficeEdition())
		{
			return website_BlockView::NONE;
		}
		
		$configuration = $this->getConfiguration();
		$comments = blog_PostService::getInstance()->getLastCommentsPublished($configuration->getBlog(), $configuration->getMaxCount());
		$request->setAttribute('comments', $comments);
		
		return website_BlockView::SUCCESS;
	}
}