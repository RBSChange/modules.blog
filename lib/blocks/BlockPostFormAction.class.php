<?php
/**
 * @author michal.olexa
 * @package modules.blog
 */
class blog_BlockPostFormAction extends website_BlockAction
{
	/**
	 * @see f_mvc_Action::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function execute($request, $response, blog_persistentdocument_post $post)
	{
		if ($this->isInBackoffice())
		{
			return website_BlockView::NONE;
		}
		
		$blog = $post->getBlog();
		if (!$blog->getDocumentService()->checkAdminPermissionsForCurrentFrontendUser($blog))
		{
			$this->addError(f_Locale::translate('&modules.blog.frontoffice.Error-no-permission-on-blog;'));
			return website_BlockView::ERROR;
		}
		
		$request->setAttribute('post', $post);
		return $this->getInputViewName();
	}
	
	/**
	 * @see f_mvc_Action::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function executeSubmit($request, $response, blog_persistentdocument_post $post)
	{
		$blog = $post->getBlog();
		if (!$blog->getDocumentService()->checkAdminPermissionsForCurrentFrontendUser($blog))
		{
			$this->addError(f_Locale::translate('&modules.blog.frontoffice.Error-no-permission-on-blog;'));
			return website_BlockView::ERROR;
		}
		
		$post->save();
		
		$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
		$page = TagService::getInstance()->getDocumentByContextualTag('contextual_website_website_modules_blog_blogedit', $website);
		$this->redirectToUrl(LinkHelper::getDocumentUrl($page, null, array('blogParam[cmpref]' => $post->getBlog()->getId())));
		return website_BlockView::NONE;
	}
	
	/**
	 * @return String[] 
	 * @see f_mvc_Action::getInputValidationRules()
	 */
	public function getSubmitInputValidationRules($request, $bean)
	{
		return BeanUtils::getBeanValidationRules('blog_persistentdocument_post', $this->getPostBeanInclude());
	}
	
	/**
	 * @return Array<String)
	 */
	public function getPostBeanInclude()
	{
		return array('label', 'startpublicationdate', 'endpublicationdate', 'postDate', 'contents', 'summary', 'blog', 'category', 'keywordsText', 'showSummaryOnDetail', 'allowComments');
	}
}