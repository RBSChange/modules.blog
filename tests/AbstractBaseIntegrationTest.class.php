<?php
/**
 * @package modules.blog.tests
 */
abstract class blog_tests_AbstractBaseIntegrationTest extends blog_tests_AbstractBaseTest
{
	/**
	 * @return void
	 */
	public function prepareTestCase()
	{
		$this->loadSQLResource('integration-test.sql', true, false);
	}
}