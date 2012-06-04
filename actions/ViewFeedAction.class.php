<?php
/**
 * blog_ViewFeedAction
 * @package modules.blog.actions
 */
class blog_ViewFeedAction extends change_Action
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{		
		$parentId = $request->getModuleParameter('blog', 'parentref');
		if (null === $parentId)
		{
			$parentId = $request->getParameter('parentref');
		}

		$parent = DocumentHelper::getDocumentInstance($parentId);
		$ds = $parent->getDocumentService();
		if (f_util_ClassUtils::methodExists($ds, 'getRSSFeedWriter'))
		{
			$feedWriter = $ds->getRSSFeedWriter($parent);
		}
		
		// Set the link, title and description of the feed
		$this->setHeaders($feedWriter, $request, $parent);
		$this->setContentType('text/xml');
		echo $feedWriter->toString();
	}
	
	/**
	 * @param change_Request $request
	 * @param rss_FeedWriter $feedWriter
	 * @param f_persistentdocument_PersistentDocument $parent
	 */
	private function setHeaders($feedWriter, $request, $parent)
	{
		$title = $parent->getLabel();
		if (f_util_ClassUtils::methodExists($parent, 'getBlog'))
		{
			$title = $parent->getBlog()->getLabel() . ' - ' . $title;
		}		
		$feedWriter->setTitle($title);
		$feedWriter->setDescription(f_util_HtmlUtils::htmlToText($parent->getDescriptionAsHtml()));

		$feedURL = LinkHelper::getDocumentUrl($parent);

		$feedWriter->setLink($feedURL);
	}
	
	/**
	 * @return boolean
	 */
	public function isSecure()
	{
		return false;
	}
	
	/**
	 * @return boolean
	 */
	protected function isDocumentAction()
	{
		return false;
	}
}