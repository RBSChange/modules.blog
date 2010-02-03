<?php
/**
 * blog_persistentdocument_preferences
 * @package blog.persistentdocument
 */
class blog_persistentdocument_preferences extends blog_persistentdocument_preferencesbase 
{
	/**
	 * @see f_persistentdocument_PersistentDocumentImpl::getLabel()
	 *
	 * @return String
	 */
	public function getLabel()
	{
		return f_Locale::translateUI(parent::getLabel());
	}
}