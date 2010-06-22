<?php
/**
 * blog_patch_0303
 * @package modules.blog
 */
class blog_patch_0303 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$this->executeLocalXmlScript('list.xml');
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
		return '0303';
	}
}