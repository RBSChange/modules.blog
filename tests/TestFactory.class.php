<?php
class blog_TestFactory extends blog_TestFactoryBase
{
	/**
	 * @var blog_TestFactory
	 */
	private static $instance;

	/**
	 * @return blog_TestFactory
	 * @throws Exception
	 */
	public static function getInstance()
	{
		if (PROFILE != 'test')
		{
			throw new Exception('This method is only usable in test mode.');
		}
		if (self::$instance === null)
		{
			self::$instance = new blog_TestFactory;
			// register the testFactory in order to be cleared after each test case.
			tests_AbstractBaseTest::registerTestFactory(self::$instance);
		}
		return self::$instance;
	}

	/**
	 * Clear the TestFactory instance.
	 * 
	 * @return void
	 * @throws Exception
	 */
	public static function clearInstance()
	{
		if (PROFILE != 'test')
		{
			throw new Exception('This method is only usable in test mode.');
		}
		self::$instance = null;
	}
	
	/**
	 * Initialize documents default properties
	 * @return void
	 */
	public function init()
	{
		$this->setBlogDefaultProperty('label', 'blog test');
		$this->setPostgroupDefaultProperty('label', 'postgroup test');
		$this->setCategoryDefaultProperty('label', 'category test');
		$this->setKeywordDefaultProperty('label', 'keyword test');
		$this->setPostfolderDefaultProperty('label', 'postfolder test');
		$this->setCategoryfolderDefaultProperty('label', 'categoryfolder test');
		$this->setKeywordfolderDefaultProperty('label', 'keywordfolder test');
		
		$this->setPostDefaultProperty('label', 'post test');
		$this->setPostDefaultProperty('contents', 'contents post test');
	}
}