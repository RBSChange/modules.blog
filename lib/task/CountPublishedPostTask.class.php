<?php
class blog_CountPublishedPostTask extends task_SimpleSystemTask
{
	/**
	 * @see task_SimpleSystemTask::execute()
	 */
	protected function execute()
	{
		$ids = blog_BlogService::getInstance()->createQuery()
			->add(Restrictions::published())
			->setProjection(Projections::property('id', 'id'))
			->findColumn('id');

		$batchPath = 'modules/blog/lib/bin/batchCountPost.php';
		foreach ($ids as $id)
		{
			if (Framework::isInfoEnabled())
			{
				Framework::info(__METHOD__ . ' count post for blog: ' . $id);		
			}	
			$result = f_util_System::execHTTPScript($batchPath, array($id));
			// Log fatal errors...
			if ($result != 'OK')
			{
				Framework::error(__METHOD__ . ' ' . $batchPath . ' unexpected result: "' . $result . '"');
			}
		}	
	}
}