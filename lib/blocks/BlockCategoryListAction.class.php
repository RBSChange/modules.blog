<?php
/**
 * blog_BlockCategoryListAction
 * @package modules.blog.lib.blocks
 */
class blog_BlockCategoryListAction extends website_BlockAction
{
	/**
	 * @see website_BlockAction::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function execute($request, $response)
	{				
		$document = $this->getDocumentParameter();
		$request->setAttribute('document', $document);
		if (!($document instanceof blog_persistentdocument_blog))
		{
			if ($document !== null && f_util_ClassUtils::methodExists($document, 'getBlog'))
			{
				$blog = $document->getBlog();
				if ($blog === null)
				{
					return website_BlockView::NONE;
				}
			}
			else 
			{
				return website_BlockView::NONE;
			}
		}
		else
		{
			$blog = $document;
		}
		if (!$blog->isPublished())
		{
			return website_BlockView::NONE;
		}
		$request->setAttribute('blog', $blog);
		return website_BlockView::SUCCESS;
	}
}