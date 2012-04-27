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

		$keywordInfos = array();
		$keywords = blog_KeywordService::getInstance()->getMostUsedByBlog($blog, intval($this->getConfiguration()->getLimit()));
		if (count($keywords))
		{
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
				$title = LocaleService::getInstance()->transFO('m.blog.frontoffice.post-count', array('ucf'), array('count' => $count));
				$keywordInfos[] = array('document' => $keyword, 'size' => $size, 'level' => $level, 'title' => $title);
			}
		}
		$request->setAttribute('keywords', $keywordInfos);
		
		return website_BlockView::SUCCESS;
	}
}