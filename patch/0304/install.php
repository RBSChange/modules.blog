<?php
/**
 * blog_patch_0304
 * @package modules.blog
 */
class blog_patch_0304 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$model = f_persistentdocument_PersistentDocumentModel::getInstance('blog', 'keyword');
		if ($model->hasProperty('postCount'))
		{
			$this->log('Compile documents ...');
			$this->execChangeCommand('compile-documents', array());
		}
		$this->log('Add modules/blog/lib/task in autoload...');
		$this->execChangeCommand('update-autoload', array('modules/blog/lib/task'));
		 
		$this->log('Compile blog locales...');
		$this->execChangeCommand('compile-locales', array('blog'));
		$this->addCountPublishedPostTask();
	}

	/**
	 * @return void
	 */
	private function addCountPublishedPostTask()
	{
		$this->log('Add blog_CountPublishedPostTask task.');
		
		$task = task_PlannedtaskService::getInstance()->createQuery()
			->add(Restrictions::eq('label', 'blog_CountPublishedPostTask'))->findUnique();

		if ($task === null)
		{
			$task = task_PlannedtaskService::getInstance()->getNewDocumentInstance();
			$task->setSystemtaskclassname('blog_CountPublishedPostTask');
			$task->setLabel('blog_CountPublishedPostTask');
			$task->setMinute(0);
			$task->setHour(4);
			$task->save(ModuleService::getInstance()->getSystemFolderId('task', 'blog'));
		}
		else
		{
			$this->log('Task blog_CountPublishedPostTask already exist.');
		}
	}
	
	/**
	 * @return String
	 */
	protected final function getModuleName()
	{
		return 'blog';
	}

	/**
	 * @return String
	 */
	protected final function getNumber()
	{
		return '0304';
	}
}