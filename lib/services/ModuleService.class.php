<?php
/**
 * @package modules.blog.lib.services
 */
class blog_ModuleService extends ModuleBaseService
{
	/**
	 * Singleton
	 * @var blog_ModuleService
	 */
	private static $instance = null;

	/**
	 * @return blog_ModuleService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}
	
	/**
	 * @param Integer $documentId
	 * @return f_persistentdocument_PersistentTreeNode
	 */
	public function getParentNodeForPermissions($documentId)
	{
		$document = DocumentHelper::getDocumentInstance($documentId);
		if ($document instanceof blog_persistentdocument_post || $document instanceof blog_persistentdocument_keyword)
		{
			$blog = $document->getBlog();
			if ($blog !== null)
			{
				return TreeService::getInstance()->getInstanceByDocumentId($blog->getId());
			}
		}
		return null;
	}
	
	/**
	 * @return array
	 */
	public function getPingUrls()
	{
		$result = array();
		$pingUrlsTxt = ModuleService::getInstance()->getPreferenceValue('blog', 'pingurls');
		if (f_util_StringUtils::isEmpty($pingUrlsTxt))
		{
			return $result;
		}
		$urls = explode(",", $pingUrlsTxt);
		foreach ($urls as $url)
		{
			$url = trim($url);
			$errors = new validation_Errors();
			$validator = new validation_UrlValidator();
			if ($validator->validate($url, $errors))
			{
				$result[] = $url;
			}
		}
		return $result;
	}
}