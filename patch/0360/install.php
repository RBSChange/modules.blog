<?php
/**
 * blog_patch_0360
 * @package modules.blog
 */
class blog_patch_0360 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$tm = f_persistentdocument_TransactionManager::getInstance();
		foreach (website_PageService::getInstance()->createQuery()->add(Restrictions::hasTag('functional_blog_blog-detail'))->find() as $page)
		{
			try
			{
				$tm->beginTransaction();
				
				/* @var $page website_persistentdocument_page */
				$page->setMetatitle('{blog_blog.blogLabel} - {website.label}');
				$page->setDescription('Blog {blog_blog.blogLabel} - {website.label} : {blog_blog.blogDescription}');
				$tm->getPersistentProvider()->updateDocument($page);
				
				$tm->commit();
			} 
			catch (Exception $e)
			{
				$tm->rollback($e);
				throw $e;
			}
		}
		
		foreach (website_PageService::getInstance()->createQuery()->add(Restrictions::hasTag('functional_blog_category-detail'))->find() as $page)
		{
			try
			{
				$tm->beginTransaction();
				
				/* @var $page website_persistentdocument_page */
				$page->setMetatitle('{blog_category.categoryLabel} - {blog_category.blogLabel} - {website.label}');
				$page->setDescription('{blog_category.categoryLabel} : billets de la catégorie {blog_category.categoryLabel} du blog {blog_category.blogLabel} sur le site {website.label}. {blog_category.categoryDescription}');
				$tm->getPersistentProvider()->updateDocument($page);
				
				$tm->commit();
			} 
			catch (Exception $e)
			{
				$tm->rollback($e);
				throw $e;
			}
		}
		
		foreach (website_PageService::getInstance()->createQuery()->add(Restrictions::hasTag('functional_blog_keyword-detail'))->find() as $page)
		{
			try
			{
				$tm->beginTransaction();
				
				/* @var $page website_persistentdocument_page */
				$page->setMetatitle('{blog_keyword.keywordLabel} - {blog_keyword.blogLabel} - {website.label}');
				$page->setDescription('Billets {blog_keyword.keywordLabel} : du blog {blog_keyword.blogLabel} sur le site {website.label}. {blog_keyword.keywordDescription}');
				$tm->getPersistentProvider()->updateDocument($page);
				
				$tm->commit();
			} 
			catch (Exception $e)
			{
				$tm->rollback($e);
				throw $e;
			}
		}
	}
}