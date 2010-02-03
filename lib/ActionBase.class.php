<?php
/**
 * @package modules.blog.lib
 */
class blog_ActionBase extends f_action_BaseAction
{

	/**
	 * Returns the blog_KeywordfolderService to handle documents of type "modules_blog/keywordfolder".
	 *
	 * @return blog_KeywordfolderService
	 */
	public function getKeywordfolderService()
	{
		return blog_KeywordfolderService::getInstance();
	}

	/**
	 * Returns the blog_PostfolderService to handle documents of type "modules_blog/postfolder".
	 *
	 * @return blog_PostfolderService
	 */
	public function getPostfolderService()
	{
		return blog_PostfolderService::getInstance();
	}

	/**
	 * Returns the blog_BlogService to handle documents of type "modules_blog/blog".
	 *
	 * @return blog_BlogService
	 */
	public function getBlogService()
	{
		return blog_BlogService::getInstance();
	}

	/**
	 * Returns the blog_KeywordService to handle documents of type "modules_blog/keyword".
	 *
	 * @return blog_KeywordService
	 */
	public function getKeywordService()
	{
		return blog_KeywordService::getInstance();
	}

	/**
	 * Returns the blog_PostService to handle documents of type "modules_blog/post".
	 *
	 * @return blog_PostService
	 */
	public function getPostService()
	{
		return blog_PostService::getInstance();
	}

	/**
	 * Returns the blog_PreferencesService to handle documents of type "modules_blog/preferences".
	 *
	 * @return blog_PreferencesService
	 */
	public function getPreferencesService()
	{
		return blog_PreferencesService::getInstance();
	}

	/**
	 * Returns the blog_PostgroupService to handle documents of type "modules_blog/postgroup".
	 *
	 * @return blog_PostgroupService
	 */
	public function getPostgroupService()
	{
		return blog_PostgroupService::getInstance();
	}

	/**
	 * Returns the blog_CategoryfolderService to handle documents of type "modules_blog/categoryfolder".
	 *
	 * @return blog_CategoryfolderService
	 */
	public function getCategoryfolderService()
	{
		return blog_CategoryfolderService::getInstance();
	}

	/**
	 * Returns the blog_CategoryService to handle documents of type "modules_blog/category".
	 *
	 * @return blog_CategoryService
	 */
	public function getCategoryService()
	{
		return blog_CategoryService::getInstance();
	}

	/**
	 * Returns the blog_YearService to handle documents of type "modules_blog/year".
	 *
	 * @return blog_YearService
	 */
	public function getYearService()
	{
		return blog_YearService::getInstance();
	}

	/**
	 * Returns the blog_MonthService to handle documents of type "modules_blog/month".
	 *
	 * @return blog_MonthService
	 */
	public function getMonthService()
	{
		return blog_MonthService::getInstance();
	}
}