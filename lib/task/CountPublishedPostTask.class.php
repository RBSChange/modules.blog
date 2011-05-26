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
		$errors = array();
		
		foreach ($ids as $id)
		{
			$this->plannedTask->ping();
			$result = f_util_System::execHTTPScript($batchPath, array($id));
			// Log fatal errors...
			if ($result != 'OK')
			{
				$errors[] = $result;
			}
		}
		
		if (count($errors))
		{
			throw new Exception(implode("\n", $errors));
		}	
	}
}