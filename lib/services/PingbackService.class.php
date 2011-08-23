<?php
/**
 * blog_PingbackService
 * @package blog
 */
class blog_PingbackService extends comment_CommentService
{
	/**
	 * @var blog_PingbackService
	 */
	private static $instance;

	/**
	 * @return blog_PingbackService
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
	 * @return blog_persistentdocument_pingback
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_blog/pingback');
	}

	/**
	 * Create a query based on 'modules_blog/pingback' model.
	 * Return document that are instance of modules_blog/pingback,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_blog/pingback');
	}
	
	/**
	 * Create a query based on 'modules_blog/pingback' model.
	 * Only documents that are strictly instance of modules_blog/pingback
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_blog/pingback', false);
	}
	
	/**
	 * @param String $sourceURI
	 * @param String $postId
	 * @return unknown
	 */
	public function hasRegisteredPingback($sourceURI, $postId)
	{
		$count = $this->createQuery()->add(Restrictions::ieq('authorwebsiteurl', $sourceURI))->add(Restrictions::eq('targetId', $postId))->setProjection(Projections::rowCount('count'))->findColumn('count');	
		return $count[0] != 0;
	}
}