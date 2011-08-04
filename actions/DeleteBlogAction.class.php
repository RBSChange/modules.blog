<?php
/**
 * blog_DeleteBlogAction
 * @package modules.blog.actions
 */
class blog_DeleteBlogAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$result = array();

		$blog = $this->getDocumentInstanceFromRequest($request);
		$blog->getDocumentService()->deleteWithContents($blog);

		return $this->sendJSON($result);
	}
}