<?php
/**
 * Class where to put your custom methods for document blog_persistentdocument_pingback
 * @package blog.persistentdocument
 */
class blog_persistentdocument_pingback extends blog_persistentdocument_pingbackbase implements indexer_IndexableDocument
{
	public function isPingback()
	{
		return true;
	}
}