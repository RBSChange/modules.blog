<?php
/**
 * blog_patch_0350
 * @package modules.blog
 */
class blog_patch_0350 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$newPath = f_util_FileUtils::buildWebeditPath('modules/blog/persistentdocument/post.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'blog', 'post');
		$newProp = $newModel->getPropertyByName('highlightingVisual');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('blog', 'post', $newProp);
		$this->execChangeCommand('compile-db-schema');
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
		return '0350';
	}
}