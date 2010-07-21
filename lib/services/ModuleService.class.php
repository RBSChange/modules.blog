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
	
	/**
	 * @param f_peristentdocument_PersistentDocument $container
	 * @param string $pageTemplate
	 * @param string $script
	 * @param DOMDocument $scriptPath
	 */
	public function updateStructureInitializationScript($container, $pageTemplate, $script, $scriptDom)
	{
		// Check container.
		if (!$container instanceof website_persistentdocument_topic)
		{
			throw new BaseException('Invalid shop', 'modules.blog.bo.actions.Invalid-topic');
		}
		else
		{
			$node = TreeService::getInstance()->getInstanceByDocument($container);
			if (count($node->getChildren('modules_website/page')) > 0)
			{
				throw new BaseException('This shop already contains pages', 'modules.blog.bo.actions.Topic-already-contains-pages');
			}
		}
		
		// Fix script content.
		$xmlWebsite = $scriptDom->getElementsByTagName('systemtopic')->item(0);
		if (!$xmlWebsite)
		{
			$xmlWebsite = $scriptDom->getElementsByTagName('topic')->item(0);
		}
		$xmlWebsite->setAttribute('documentid', $container->getId());
	}
}