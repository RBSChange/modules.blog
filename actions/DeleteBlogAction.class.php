<?php
/**
 * blog_DeleteBlogAction
 * @package modules.blog.actions
 */
class blog_DeleteBlogAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$result = array();

		$blog = $this->getDocumentInstanceFromRequest($request);
		$blog->getDocumentService()->deleteWithContents($blog);

		return $this->sendJSON($result);
	}
}