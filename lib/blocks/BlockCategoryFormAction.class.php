<?php
/**
 * @author michal.olexa
 * @package modules.blog
 */
class blog_BlockCategoryFormAction extends website_BlockAction
{
	/**
	 * @see f_mvc_Action::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function execute($request, $response, blog_persistentdocument_category $category)
	{
		if ($this->isInBackoffice())
		{
			return website_BlockView::NONE;
		}
		
		$blog = $category->getBlog();
		if (!$blog->getDocumentService()->checkAdminPermissionsForCurrentFrontendUser($blog))
		{
			$this->addError(f_Locale::translate('&modules.blog.frontoffice.Error-no-permission-on-blog;'));
			return website_BlockView::ERROR;
		}
		
		$request->setAttribute("category", $category);
		return $this->getInputViewName();
	}
	
	/**
	 * @see f_mvc_Action::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function executeSubmit($request, $response, blog_persistentdocument_category $category)
	{
		$blog = $category->getBlog();
		if (!$blog->getDocumentService()->checkAdminPermissionsForCurrentFrontendUser($blog))
		{
			$this->addError(f_Locale::translate('&modules.blog.frontoffice.Error-no-permission-on-blog;'));
			return website_BlockView::ERROR;
		}
		
		if ($category->isNew())
		{
			$parentId = blog_CategoryfolderService::getInstance()->getByBlog($category->getBlog())->getId();
		}
		else
		{
			$parentId = null;
		}
		
		$user = users_UserService::getInstance()->getCurrentFrontEndUser();
		if (f_permission_PermissionService::getInstance()->hasPermission($user, "modules_blog.FrontAdmin", $category->getBlog()->getId()))
		{
			$category->save($parentId);
		}
		
		$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
		$page = TagService::getInstance()->getDocumentByContextualTag('contextual_website_website_modules_blog_blogedit', $website);
		$this->redirectToUrl(LinkHelper::getDocumentUrl($page, null, array('blogParam[cmpref]' => $category->getBlog()->getId())));
		return website_BlockView::NONE;
	}
	
	/**
	 * @return String[] 
	 * @see f_mvc_Action::getInputValidationRules()
	 */
	public function getSubmitInputValidationRules($request, $bean)
	{
		return BeanUtils::getBeanValidationRules('blog_persistentdocument_category', $this->getCategoryBeanInclude());
	}
	
	/**
	 * @return Array<String)
	 */
	public function getCategoryBeanInclude()
	{
		return array('label', 'description', 'blog');
	}
}