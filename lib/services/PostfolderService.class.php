<?php
/**
 * blog_PostfolderService
 * @package blog
 */
class blog_PostfolderService extends generic_FolderService
{
	/**
	 * @var blog_PostfolderService
	 */
	private static $instance;

	/**
	 * @return blog_PostfolderService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @return blog_persistentdocument_postfolder
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_blog/postfolder');
	}

	/**
	 * Create a query based on 'modules_blog/postfolder' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_blog/postfolder');
	}
	
	/**
	 * Create a query based on 'modules_blog/postfolder' model.
	 * Only documents that are strictly instance of modules_blog/postfolder
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_blog/postfolder', false);
	}
	
	/**
	 * @param blog_persistentdocument_blog $blog
	 * @return blog_persistentdocument_postfolder
	 */
	public function getByBlog($blog)
	{
		return $this->createStrictQuery()->add(Restrictions::childOf($blog->getId()))->findUnique();
	}
}