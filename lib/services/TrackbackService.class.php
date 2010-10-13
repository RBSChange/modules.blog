<?php
/**
 * blog_TrackbackService
 * @package blog
 */
class blog_TrackbackService extends comment_CommentService
{
	/**
	 * @var blog_TrackbackService
	 */
	private static $instance;

	/**
	 * @return blog_TrackbackService
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
	 * @return blog_persistentdocument_trackback
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_blog/trackback');
	}

	/**
	 * Create a query based on 'modules_blog/trackback' model.
	 * Return document that are instance of modules_blog/trackback,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_blog/trackback');
	}
	
	/**
	 * Create a query based on 'modules_blog/trackback' model.
	 * Only documents that are strictly instance of modules_blog/trackback
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_blog/trackback', false);
	}
}