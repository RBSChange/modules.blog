<?php
/**
 * @package modules.blog
 */
class blog_GetTreeChildrenJSONAction extends generic_GetTreeChildrenJSONAction
{
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param string[] $subModelNames
	 * @param string $propertyName
	 * @return array<f_persistentdocument_PersistentDocument>
	 */
	protected function getVirtualChildren($document, $subModelNames, $propertyName)
	{
		if (f_util_ClassUtils::methodExists($document, 'getBOChildren'))
    	{
			return $document->getBOChildren($document);
    	}
		else
		{
			return parent::getVirtualChildren($document, $subModelNames, $propertyName);
		}
	}
}
