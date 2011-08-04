<?php
/**
 * @author intportg
 * @package modules.blog.lib.services
 */
class blog_ListCategoriesbyblogService extends BaseService implements list_ListItemsService
{
	/**
	 * @var form_ListMarkupService
	 */
	private static $instance;

	/**
	 * @return website_ListTemplatesService
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
	 * @return array
	 */
	public function getItems()
	{
		try 
		{
			$request = change_Controller::getInstance()->getContext()->getRequest();
			$parentId = intval($request->getParameter('parentId', 0));
			$parent = DocumentHelper::getDocumentInstance($parentId);
			if ($parent instanceof blog_persistentdocument_blog)
			{
				$blog = $parent;
			}
			else if (f_util_ClassUtils::methodExists($parent, 'getBlog'))
			{
				$blog = $parent->getBlog();
			}
			else 
			{
				if (Framework::isDebugEnabled())
				{
					Framework::debug(__METHOD__ . ' Unexpected document type for id: ' . $parentId);
				}
				return array();
			}
			
			$categoryFolder = blog_CategoryfolderService::getInstance()->getByBlog($blog);
		}
		catch (Exception $e)
		{
			if (Framework::isDebugEnabled())
			{
				Framework::debug(__METHOD__ . ' EXCEPTION: ' . $e->getMessage());
			}
			return array();
		}
		
		$items = array();
		foreach ($this->getCategoriesByParent($categoryFolder) as $category)
		{
			$this->addItems($items, $category, '');
		}
		return $items;
	}
	
	/**
	 * @param list_Item[] $items
	 * @param blog_persistentodcument_category $category
	 * @param string $prefix
	 */
	private function addItems(&$items, $category, $prefix)
	{
		$items[] = new list_Item(
			($prefix ? ($prefix . ' ') : '') . $category->getLabel(),
			$category->getId()
		);
		
		foreach ($this->getCategoriesByParent($category) as $subCategory)
		{
			$this->addItems($items, $subCategory, $prefix.'-');
		}
	}
	
	/**
	 * @param f_persistentdocument_PersistentDocument $parent
	 * @return blog_persistentdocument_category
	 */
	private function getCategoriesByParent($parent)
	{
		return blog_CategoryService::getInstance()->createQuery()->add(Restrictions::childOf($parent->getId()))->find();
	}
}