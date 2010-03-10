<?php
/**
 * Class where to put your custom methods for document blog_persistentdocument_trackback
 * @package blog.persistentdocument
 */
class blog_persistentdocument_trackback extends blog_persistentdocument_trackbackbase implements indexer_IndexableDocument
{	
	public function isTrackbackback()
	{
		return true;
	}
}