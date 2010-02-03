<?php
/**
 * @author intportg
 * @package modules.blog.lib
 */
class blog_XmlListTreeParser extends tree_parser_XmlListTreeParser
{
	/**
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param Integer $level
	 * @return array<f_persistentdocument_PersistentDocument>
	 */
	protected function getTreeChildren($document, $level)
	{
		if (f_util_ClassUtils::methodExists($document, 'getChildrenForParser'))
    	{
			$childComponents = array();
			foreach ($document->getChildrenForParser($document) as $child)
			{
				$childComponents[$child->getId()] = $child;
			}
			return $childComponents;
    	}
    	else
    	{
   			return parent::getTreeChildren($document, $level);
    	}
	}
}