<?php
class blog_PingBlogsTask extends task_SimpleSystemTask  
{
	/**
	 * @see task_SimpleSystemTask::execute()
	 *
	 */
	protected function execute()
	{
		$postIds = $this->getParameter('postIds');
		foreach ($postIds as $postId) 
		{
			try 
			{
				$post = DocumentHelper::getDocumentInstance($postId);
				$post->getDocumentService()->pingbacksForPost($post);
				$post->getDocumentService()->trackbacksForPost($post);
			}
			catch (Exception $e)
			{
				Framework::exception($e);
			}
		}
		
	}
}
