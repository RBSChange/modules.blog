<?php
/**
 * blog_patch_0300
 * @package modules.blog
 */
class blog_patch_0300 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		parent::execute();
		$newPath = f_util_FileUtils::buildWebeditPath('modules/blog/persistentdocument/preferences.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'blog', 'preferences');
		$newProp = $newModel->getPropertyByName('pingurls');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('blog', 'preferences', $newProp);
		
		$newPath = f_util_FileUtils::buildWebeditPath('modules/blog/persistentdocument/blog.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'blog', 'blog');
		$newProp = $newModel->getPropertyByName('pingurls');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('blog', 'blog', $newProp);
		exec("change.php cd");
		$this->executeSQLQuery("ALTER TABLE m_blog_doc_post ADD `document_correctionid` int(11)");
		$this->executeSQLQuery("ALTER TABLE m_blog_doc_post ADD `document_correctionofid` int(11)");
		$this->executeLocalXmlScript('pinglist.xml');
		exec("change.php ci18n blog");
		
		exec("change.php import-data blog init.xml");
		
		$prefs = ModuleService::getInstance()->getPreferencesDocument('blog');
		$prefs->setPingurls("http://blogsearch.google.com/ping/RPC2,http://rpc.pingomatic.com,http://ping.feedburner.com,http://rpc.technorati.com/rpc/ping");
		$prefs->save();
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
		return '0300';
	}
}