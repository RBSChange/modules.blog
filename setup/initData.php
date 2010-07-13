<?php
/**
 * @package modules.blog.setup
 */
class blog_Setup extends object_InitDataSetup
{
	public function install()
	{
		$this->executeModuleScript('init.xml');
		$this->addCountPublishedPostTask();
	}
		
	/**
	 * @return void
	 */
	private function addCountPublishedPostTask()
	{
		$task = task_PlannedtaskService::getInstance()->getNewDocumentInstance();
		$task->setSystemtaskclassname('blog_CountPublishedPostTask');
		$task->setLabel('blog_CountPublishedPostTask');
		$task->setMinute(0);
		$task->setHour(4);
		$task->save(ModuleService::getInstance()->getSystemFolderId('task', 'blog'));
	}
}