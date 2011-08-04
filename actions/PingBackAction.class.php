<?php
/**
 * blog_PingBackAction
 * @package modules.blog.actions
 */
class blog_PingBackAction extends change_Action
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		if (!f_util_ClassUtils::classExists("XML_RPC2_Client"))
		{
			Framework::warn(__METHOD__ . " XML_RPC2_Client not installed, please run change.php --deep-check to install module dependency");
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