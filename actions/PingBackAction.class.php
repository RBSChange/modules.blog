<?php
/**
 * blog_PingBackAction
 * @package modules.blog.actions
 */
class blog_PingBackAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		if (!blog_ModuleService::getInstance()->checkXmlRpc())
		{
			Framework::warn(__METHOD__ . " XML_RPC2_Client not functional");
			return;
		}
		$server = XML_RPC2_Server::create('blog_PingBackServer', array('prefix' => 'pingback.'));
		$server->handleCall();
	}
	
	public function isSecure()
	{
		return false;
	}
}