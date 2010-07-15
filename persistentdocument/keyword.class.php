<?php
/**
 * blog_persistentdocument_keyword
 * @package blog.persistentdocument
 */
class blog_persistentdocument_keyword extends blog_persistentdocument_keywordbase
{		
	/**
	 * @param string $moduleName
	 * @param string $treeType
	 * @param array<string, string> $nodeAttributes
	 */
	protected function addTreeAttributes($moduleName, $treeType, &$nodeAttributes)
	{
			
		$query = blog_PostService::getInstance()->createQuery()
			->add(Restrictions::eq('keyword', $this))
			->setProjection(Projections::rowCount('count'));
				
		if (f_persistentdocument_PersistentDocumentModel::getInstance("blog", "post")->useCorrection())
		{
			$query->add(Restrictions::isNull('correctionofid'));
		}
		$result = $query->findUnique();			
		$nodeAttributes['postCount'] = $result['count'];
		$nodeAttributes['publishedPostCount'] = $this->getPublishedPostCount();
	}
}