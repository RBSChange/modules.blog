<?php
class blog_ListPingsService extends BaseService implements list_ListItemsService
{
	/**
	 * @var blog_ListPingsService
	 */
	private static $instance;
	

	/**
	 * @return blog_ListPingsService
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
		foreach (blog_ModuleService::getInstance()->getPingUrls() as $url)
		{
			$items[] = new list_Item($url, $url);
		}
		return $items;
	}

}