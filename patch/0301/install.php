<?php
/**
 * blog_patch_0301
 * @package modules.blog
 */
class blog_patch_0301 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		parent::execute();
		$newPath = f_util_FileUtils::buildWebeditPath('modules/blog/persistentdocument/blog.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'blog', 'blog');
		$newProp = $newModel->getPropertyByName('enablepingback');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('blog', 'blog', $newProp);
		
		$newPath = f_util_FileUtils::buildWebeditPath('modules/blog/persistentdocument/blog.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'blog', 'blog');
		$newProp = $newModel->getPropertyByName('enabletrackback');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('blog', 'blog', $newProp);
		
		$newPath = f_util_FileUtils::buildWebeditPath('modules/blog/persistentdocument/post.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'blog', 'post');
		$newProp = $newModel->getPropertyByName('trackbacks');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('blog', 'post', $newProp);
		
		$newPath = f_util_FileUtils::buildWebeditPath('modules/blog/persistentdocument/post.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'blog', 'post');
		$newProp = $newModel->getPropertyByName('allowPingbacks');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('blog', 'post', $newProp);
		
		$newPath = f_util_FileUtils::buildWebeditPath('modules/blog/persistentdocument/post.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'blog', 'post');
		$newProp = $newModel->getPropertyByName('allowTrackbacks');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('blog', 'post', $newProp);
	}
	
	/**
	 * Returns the name of the module the patch belongs to.
	 *
	 * @return String
	 */
	protected final function getModuleName()
	{
		return 'blog';
	}
	
	/**
	 * Returns the number of the current patch.
	 * @return String
	 */
	protected final function getNumber()
	{
		return '0301';
	}
}