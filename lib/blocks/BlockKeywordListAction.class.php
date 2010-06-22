<?php
/**
 * blog_BlockKeywordListAction
 * @package modules.blog.lib.blocks
 */
class blog_BlockKeywordListAction extends website_BlockAction
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
			return website_BlockView::NONE;
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

		$keywordInfos = array();
		if ($blog !== null)
		{
			$keywords = blog_KeywordService::getInstance()->getMostUsedByBlog($blog, intval($this->getConfiguration()->getLimit()));  
			$maxCount = f_util_ArrayUtils::firstElement($keywords)->getPublishedPostCount();
			$minCount = f_util_ArrayUtils::lastElement($keywords)->getPublishedPostCount();
			uasort($keywords, create_function('$a, $b', 'return strnatcasecmp($a->getLabel(), $b->getLabel());'));
	
			$step = 1 / max($maxCount-$minCount, 1);
			foreach ($keywords as $keyword)
			{
				$count = $keyword->getPublishedPostCount();
				$weight = (($count - $minCount) * $step);
				$size = 1 + $weight;
				$level = min(1 + floor($weight * 3), 3); // Level may equals 1, 2 or 3.
				$title = f_Locale::translate('&modules.blog.frontoffice.Post-count;', array('count' => $count));
				$keywordInfos[] = array('document' => $keyword, 'size' => $size, 'level' => $level, 'title' => $title);
			}
		}
		$request->setAttribute('keywords', $keywordInfos);
		
		return website_BlockView::SUCCESS;
	}
}