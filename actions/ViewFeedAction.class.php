<?php
/**
 * blog_ViewFeedAction
 * @package modules.blog.actions
 */
class blog_ViewFeedAction extends blog_Action
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{		
		$parentId = $request->getModuleParameter('blog', K::PARENT_ID_ACCESSOR);
		if (null === $parentId)
		{
			$parentId = $request->getParameter(K::PARENT_ID_ACCESSOR);
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
	 * @param Request $request
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
		$feedWriter->setDescription($parent->getDescriptionAsHtml());

		$feedURL = LinkHelper::getUrl($parent);

		$feedWriter->setLink($feedURL);
	}
	
	public function isSecure()
	{
		return false;
	}
	
	protected function suffixSecureActionByDocument()
	{
		return false;
	}
}