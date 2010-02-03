<?php
/**
 * @package modules.blog.tests
 */
abstract class blog_tests_AbstractBaseFunctionalTest extends blog_tests_AbstractBaseTest
{
	/**
	 * @return void
	 */
	public function prepareTestCase()
	{
		$this->loadSQLResource('functional-test.sql', true, false);
	}
}