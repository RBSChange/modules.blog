<?php
$blogId = $_POST['argv'][0];
change_Controller::newInstance('change_Controller');
$tm = f_persistentdocument_TransactionManager::getInstance();
try
{
	$tm->beginTransaction();
	$groups = blog_PostgroupService::getInstance()
		->createQuery()->add(Restrictions::eq('blog.id', $blogId))->find();	
	foreach ($groups as $group) 
	{
		$group->getDocumentService()->updatePostCount($group);
	}
	$tm->commit();
}
catch (Exception $e)
{
	$tm->rollBack($e);
}
echo 'OK';