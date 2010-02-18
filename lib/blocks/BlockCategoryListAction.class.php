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
		if ($this->isInBackoffice())
		{
			return website_BlockView::BACKOFFICE;
		}
				
		$document = $this->getDocumentParameter();
		$request->setAttribute('document', $document);
		if (!($document instanceof blog_persistentdocument_blog))
		{
			if ($document !== null && f_util_ClassUtils::methodExists($document, 'getBlog'))
			{
				$blog = $document->getBlog();
			}
			else 
			{
				$blog = null;
			}
		}
		else
		{
			$blog = $document;
		}
		$request->setAttribute('blog', $blog);

		return website_BlockView::SUCCESS;
	}
}