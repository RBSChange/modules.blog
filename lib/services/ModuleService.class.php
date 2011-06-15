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
	 * @param array $attributes
	 * @param string $script
	 * @return array
	 */
	public function getStructureInitializationAttributes($container, $attributes, $script)
	{
		switch ($script)
		{
			case 'topicDefaultStructure':
				return $this->getBlogStructureInitializationAttributes($container, $attributes, $script);
				
			case 'frontadminDefaultStructure' :
				return $this->getFrontadminStructureInitializationAttributes($container, $attributes, $script);
			
			default:
				throw new BaseException('Unknown structure initialization script: '.$script, 'modules.blog.bo.actions.Unknown-structure-initialization-script', array('script' => $script));
		}
	}

	/**
	 * @param f_peristentdocument_PersistentDocument $container
	 * @param array $attributes
	 * @param string $script
	 * @return array
	 */
	public function getBlogStructureInitializationAttributes($container, $attributes, $script)
	{
		// Check container.
		if (!$container instanceof website_persistentdocument_topic)
		{
			throw new BaseException('Invalid topic', 'modules.blog.bo.actions.Invalid-topic');
		}
		
		$node = TreeService::getInstance()->getInstanceByDocument($container);
		if (count($node->getChildren('modules_website/page')) > 0)
		{
			throw new BaseException('This topic already contains pages', 'modules.blog.bo.actions.Topic-already-contains-pages');
		}
		
		// Set atrtibutes.
		$attributes['byDocumentId'] = $container->getId();
		return $attributes;
	}
	
	/**
	 * @param f_peristentdocument_PersistentDocument $container
	 * @param array $attributes
	 * @param string $script
	 * @return array
	 */
	public function getFrontadminStructureInitializationAttributes($container, $attributes, $script)
	{
		// Check container.
		if (!$container instanceof website_persistentdocument_website)
		{
			throw new BaseException('Invalid website', 'modules.blog.bo.actions.Invalid-website');
		}
		
		if (TagService::getInstance()->hasDocumentByContextualTag('contextual_website_website_modules_blog_blogedit', $container) || 
			TagService::getInstance()->hasDocumentByContextualTag('contextual_website_website_modules_blog_postform', $container) ||
			TagService::getInstance()->hasDocumentByContextualTag('contextual_website_website_modules_blog_categoryform', $container) ||
			TagService::getInstance()->hasDocumentByContextualTag('contextual_website_website_modules_blog_commentsvalidation', $container))
		{
			throw new BaseException('This website already contains front admin pages', 'modules.blog.bo.actions.Website-already-contains-frontadmin-page');
		}
		
		// Set atrtibutes.
		$attributes['byDocumentId'] = $container->getId();
		return $attributes;
	}
}