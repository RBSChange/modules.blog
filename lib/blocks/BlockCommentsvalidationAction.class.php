<?php
/**
 * @author michal.olexa
 * @package modules.blog
 */
class blog_BlockCommentsvalidationAction extends website_BlockAction
{
	/**
	 * @see f_mvc_Action::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function execute($request, $response)
	{
		$blog = $this->getDocumentParameter();
		if (!($blog instanceof blog_persistentdocument_blog) || !$blog->isPublished())
		{
			return website_BlockView::NONE;
		}		
		return $this->getInputViewName();
	}
	
	/**
	 * @see f_mvc_Action::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function executeValidate($request, $response)
	{
		$task = DocumentHelper::getDocumentInstance($request->getParameter('taskId'));
		if ($task->getUser()->getId() == users_UserService::getInstance()->getCurrentFrontEndUser()->getId())
		{
			task_UsertaskService::getInstance()->execute($task, 'ACCEPTED', '');
		}
		return $this->getInputViewName();
	}
	
	/**
	 * @see f_mvc_Action::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function executeCancel($request, $response)
	{
		$task = DocumentHelper::getDocumentInstance($request->getParameter('taskId'));
		if ($task->getUser()->getId() == users_UserService::getInstance()->getCurrentFrontEndUser()->getId())
		{
			task_UsertaskService::getInstance()->execute($task, 'REFUSED', '');
		}
		return $this->getInputViewName();
	}
}