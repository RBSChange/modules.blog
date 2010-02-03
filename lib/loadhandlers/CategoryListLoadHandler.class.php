<?php
class blog_CategoryListLoadHandler extends website_ViewLoadHandlerImpl
{
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 */
	function execute($request, $response)
	{
		$query = blog_CategoryService::getInstance()->createStrictQuery();
		
		$post = $request->getAttribute('post');
		if($post && $post->getBlog())
		{
			$query->add(Restrictions::eq('blog', $post->getBlog()->getId()));
		}
		
		$categories = $query->find();
		
		$request->setAttribute('categories', $categories);
		
		if($post)
		{
			$request->setAttribute('selectedCategories', implode(',', DocumentHelper::getIdArrayFromDocumentArray($post->getCategoryArray())));
		}
	}
}