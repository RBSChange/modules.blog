<?php
/**
 * blog_PreferencesService
 * @package blog
 */
class blog_PreferencesService extends f_persistentdocument_DocumentService
{
	/**
	 * @var blog_PreferencesService
	 */
	private static $instance;

	/**
	 * @return blog_PreferencesService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @return blog_persistentdocument_preferences
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_blog/preferences');
	}

	/**
	 * Create a query based on 'modules_blog/preferences' model.
	 * Return document that are instance of modules_blog/preferences,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_blog/preferences');
	}
	
	/**
	 * Create a query based on 'modules_blog/preferences' model.
	 * Only documents that are strictly instance of modules_blog/preferences
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_blog/preferences', false);
	}
	
	/**
	 * @param blog_persistentdocument_preferences $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId)
	{
		$document->setLabel('blog');
		$validUrls = blog_ModuleService::getInstance()->getPingUrls();
		foreach (blog_BlogService::getInstance()->createQuery()->find() as $blog)
		{
			$oldPingArray = $blog->getPingurlsArray();
			$newPingArray = array_intersect($validUrls, $oldPingArray);
			if (count($newPingArray) != count($oldPingArray))
			{
				$blog->setPingurlsArray($newPingArray);
				$blog->save();
			}
		}
	}
}