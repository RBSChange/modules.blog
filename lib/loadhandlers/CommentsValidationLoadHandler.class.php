<?php
class blog_CommentsValidationLoadHandler extends website_ViewLoadHandlerImpl
{
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 */
	function execute($request, $response)
	{
		$blog = $this->getDocumentParameter();
		$request->setAttribute('blog', $blog);
		
		list ($commentIds, $comments) = $this->getComments($blog);
		
		// Set the paginator.
		$pageIndex = $request->getParameter(paginator_Paginator::REQUEST_PARAMETER_NAME, 1);
		$itemsPerPage = $this->getNbItemPerPage($request, $response);
		$paginator = new paginator_Paginator('blog', $pageIndex, $comments, $itemsPerPage);
		
		$request->setAttribute('paginator', $paginator);
		$request->setAttribute('commentIds', $commentIds);
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 * @return Array
	 */
	private function getComments($blog)
	{
		$tasks = $this->getPendingTasksForCurrentFrontendUser();
		$documentIds = array();
		foreach ($tasks as $task)
		{
			$documentIds[$task->getId()] = $task->getWorkitem()->getDocumentid();
		}
		$targetIds = DocumentHelper::getIdArrayFromDocumentArray($blog->getPostArrayInverse());
		
		if (f_util_ArrayUtils::isNotEmpty($documentIds) && f_util_ArrayUtils::isNotEmpty($targetIds))
		{
			$query = comment_CommentService::getInstance()->createQuery();
			$query->add(Restrictions::in('id', $documentIds));
			$query->add(Restrictions::in('targetId', $targetIds));
			$comments = $query->find();
		}
		else
		{
			$comments = array();
		}
		
		return array($documentIds, $comments);
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
	
	/**
	 * @return array<task_persistentdocument_usertask>
	 */
	private function getPendingTasksForCurrentFrontendUser()
	{
		$user = users_UserService::getInstance()->getCurrentFrontEndUser();
		if ($user !== null)
		{
			$query = f_persistentdocument_PersistentProvider::getInstance()->createQuery('modules_task/usertask');
			$query->add(Restrictions::eq('user', $user->getId()));
			$query->add(Restrictions::published());
			$query->addOrder(Order::desc('document_creationdate'));
			$query->setMaxResults(50);
			return $query->find();
		}
		
		return array();
	}
}