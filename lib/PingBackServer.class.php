<?php
class blog_PingBackServer
{
	/**	
	 * @param String $sourceURI
	 * @param String $targetURI
	 * @return String
	 */
	public static function ping($sourceURI, $targetURI)
	{
		$globalRequest = change_Controller::getInstance()->getContext()->getRequest();
		$postId = $globalRequest->getModuleParameter('blog', 'postId');
		if ($postId === null)
		{
			throw new XML_RPC2_FaultException("Access denied : you're probably not using the correct XML-RPC ping url", 49);
		}
		try 
		{
			$post = DocumentHelper::getDocumentInstance($postId);
		}
		catch (Exception $e)
		{
			Framework::exception($e);
			throw new XML_RPC2_FaultException("Access denied : you're probably not using the correct XML-RPC ping url", 49);
		}
		
		if ($post->getAllowPingbacks() === false)
		{
			throw new XML_RPC2_FaultException("Access denied : you're probably not using the correct XML-RPC ping url", 49);
		}
		
		if (LinkHelper::getDocumentUrl($post) !== $targetURI || !$post->isPublished())
		{
			throw new XML_RPC2_FaultException("targetURI not recognized", 33);
		}
		$client = change_HttpClientService::getInstance()->getNewHttpClient();
		$client->setUri($sourceURI);
		$request = $client->request();
		$content = $request->getBody();
		if ($request->getStatus() != 200)
		{
			if (Framework::isDebugEnabled())
			{
				Framework::debug(__METHOD__ . ' Ping received from $sourceURI, but returns status ' . $client->getHTTPReturnCode());
			}
			throw new XML_RPC2_FaultException("sourceURI does not exist or is not reachable", 16);
		}

		$excerpt = blog_PingHelper::excerptFromContent($data, $targetURI);
		if (f_util_StringUtils::isEmpty($excerpt))
		{
			throw new XML_RPC2_FaultException("no link to targetURI", 17);
		}
		$pbs = blog_PingbackService::getInstance();
		if ($pbs->hasRegisteredPingback($sourceURI, $postId))
		{
			throw new XML_RPC2_FaultException("The pingback has already been registered.", 48);
		}
		$pingback = $pbs->getNewDocumentInstance();
		$pingback->setAuthorName(blog_PingHelper::authorFromContent($data));
		$pingback->setAuthorwebsiteurl($sourceURI);
		$pingback->setEmail(Framework::getDefaultNoReplySender());
		$pingback->setContents('[...] ' . $excerpt . ' [...]');
		$pingback->setTargetId($postId);
		$pingback->save();
		// Ask validation.
		$pingback->getDocumentService()->frontendValidation($pingback);
		return "Successful ping of $targetURI from $sourceURI";
	}
}
