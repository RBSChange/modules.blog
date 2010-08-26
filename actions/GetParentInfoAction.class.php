<?php
/**
 * blog_GetParentInfoAction
 * @package modules.blog.actions
 */
class blog_GetParentInfoAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		try 
		{
			$document = $this->getDocumentInstanceFromRequest($request);
			if ($document instanceof blog_persistentdocument_category)
			{
				$result['id'] = $document->getId();
				$result['label'] = $document->getLabel();
				$result['type'] = 'category';
			}
			else if ($document instanceof blog_persistentdocument_keyword)
			{
				$result['id'] = $document->getId();
				$result['label'] = $document->getLabel();
				$result['type'] = 'keyword';
			}
		}
		catch (Exception $e)
		{
			// Nothing to return. 
			if (Framework::isDebugEnabled())
			{
				Framework::exception($e);
			}
		}
		return $this->sendJSON($result);
	}
}