<?php
/**
 * @package modules.blog.tests
 */
abstract class blog_tests_AbstractBaseUnitTest extends blog_tests_AbstractBaseTest
{
	/**
	 * @return void
	 */
	public function prepareTestCase()
	{
		$this->resetDatabase();
	}
}