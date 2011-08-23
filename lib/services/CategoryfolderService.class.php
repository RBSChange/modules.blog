<?php
/**
 * blog_CategoryfolderService
 * @package blog
 */
class blog_CategoryfolderService extends generic_FolderService
{
	/**
	 * @var blog_CategoryfolderService
	 */
	private static $instance;

	/**
	 * @return blog_CategoryfolderService
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
	 * @return blog_persistentdocument_categoryfolder
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_blog/categoryfolder');
	}

	/**
	 * Create a query based on 'modules_blog/categoryfolder' model
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_blog/categoryfolder');
	}
	
	/**
	 * Create a query based on 'modules_blog/categoryfolder' model.
	 * Only documents that are strictly instance of modules_blog/categoryfolder
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_blog/categoryfolder', false);
	}
		
	/**
	 * @param blog_persistentdocument_blog $blog
	 * @return blog_persistentdocument_categoryfolder
	 */
	public function getByBlog($blog)
	{
		return $this->createStrictQuery()->add(Restrictions::childOf($blog->getId()))->findUnique();
	}
}