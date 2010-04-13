<?php
/**
 * blog_patch_0302
 * @package modules.blog
 */
class blog_patch_0302 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		// Implement your patch here.
		$newPath = f_util_FileUtils::buildWebeditPath('modules/blog/persistentdocument/keyword.xml');
		$newModel = generator_PersistentModel::loadModelFromString(f_util_FileUtils::read($newPath), 'blog', 'keyword');
		$newProp = $newModel->getPropertyByName('comparablelabel');
		f_persistentdocument_PersistentProvider::getInstance()->addProperty('blog', 'keyword', $newProp);
		
		$toDelete = array();
		$ks = blog_KeywordService::getInstance();
		foreach ($ks->createQuery()->find() as $keyword)
		{
			$keyword->setModificationdate(null);
			$keyword->save();
			$toDelete[$keyword->getId()] = $keyword;
		}
		
		$ps = blog_PostService::getInstance();
		foreach ($ps->createQuery()->find() as $post)
		{
			$post->setKeywordArray(array());
			
			// Add all keywords from the keywordsText property.
			$ts = blog_KeywordService::getInstance();
			$blog = $post->getBlog();
			foreach (explode(',', $post->getKeywordsText()) as $keywordLabel)
			{
				$keywordLabel = trim($keywordLabel);
				if ($keywordLabel != '')
				{
					$keyword = $ts->getByLabelInBlog($keywordLabel, $blog);
					$post->addKeyword($keyword);
					if (isset($toDelete[$keyword->getId()]))
					{
						unset($toDelete[$keyword->getId()]);
					}
					$ks = blog_KeywordService::getInstance();
					$ks->refreshPostCount($keyword);
					$ks->refreshPublishedPostCount($keyword);
				}
			}
			try
			{
				$this->beginTransaction();
				$this->getPersistentProvider()->updateDocument($post);
				$this->commit();
			}
			catch (Exception $e)
			{
				$this->rollBack($e);
			}
		}
		
		foreach ($toDelete as $kw)
		{
			echo "Deleting " . $kw->getLabel() . "\n";
			$kw->delete();
		}
	

	}
	
	/**
	 * @return String
	 */
	protected final function getModuleName()
	{
		return 'blog';
	}
	
	/**
	 * @return String
	 */
	protected final function getNumber()
	{
		return '0302';
	}
}