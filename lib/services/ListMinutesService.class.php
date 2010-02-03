<?php
/**
 * @author intportg
 * @package modules.blog.lib.services
 */
class blog_ListMinutesService extends BaseService implements list_ListItemsService
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
		$items = array();
		for ($i = 0; $i < 60; $i++)
		{
			$items[] = new list_Item($i, $i);
		}
		return $items;
	}
}