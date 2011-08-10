<?php
/**
 * blog_TrackBackAction
 * @package modules.blog.actions
 */
class blog_TrackBackAction extends change_Action
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$error = false;
		$errorMessage = "Unknown error";
		$post = null;
		if ($request->hasParameter('cmpref'))
		{
			$post = DocumentHelper::getDocumentInstance($request->getParameter('cmpref'));
		}
		else if ($request->hasModuleParameter('blog', 'cmpref'))
		{
			$post = DocumentHelper::getDocumentInstance($request->getModuleParameter('blog', 'cmpref'));
		}
		if (!$post instanceof blog_persistentdocument_post || $post->getAllowTrackbacks() === false)
		{
			$context->getController()->forward('website', 'Error404');
			return;
		}
		
		// Ok let's go
		if (!$request->hasParameter('url'))
		{
			$this->handlePingError("No url specified");
			return;
		}	
		$postUrl = LinkHelper::getDocumentUrl($post);
		$url = $request->getParameter('url');
		$client = change_HttpClientService::getInstance()->getNewHttpClient(); 
		$client->getUri($url);
		$request = $client->request();
		$data = $request->getBody();
		if ($request->getStatus() != 200)
		{
			$this->handlePingError("Bad url");
			return;
		}
		$postId = $post->getId();
		$trackback = blog_TrackbackService::getInstance()->getNewDocumentInstance();
		$trackback->setAuthorName($request->getParameter('blog_name', blog_PingHelper::authorFromContent($data)));
		$trackback->setAuthorwebsiteurl($url);
		$trackback->setEmail(Framework::getDefaultNoReplySender());
		$excerpt = $request->getParameter("excerpt", blog_PingHelper::excerptFromContent($data, $postUrl));
		if ($excerpt === null)
		{
			$excerpt = f_Locale::translate('modules.blog.frontoffice.no-excerpt-for-trackback');
		}
		$trackback->setContents($request->getParameter('title', '') . ' : " [...] ' .  $excerpt  . ' [...] "');
		$trackback->setTargetId($postId);
		$trackback->save();
		// Ask validation.
		$trackback->getDocumentService()->frontendValidation($trackback);
		$this->writeAnswer($error, $errorMessage);
	}
	
	private function handlePingError($message)
	{
		$this->writeAnswer(true, $message);
	}
	
	/**
	 * @param String $error
	 * @param String $errorMessage
	 */
	private function writeAnswer($error, $errorMessage)
	{
		$this->setContentType('text/xml');
		$writer = new XMLWriter();
		$writer->openMemory();
		$writer->startDocument("1.0");
		$writer->startElement('response');
		if ($error)
		{
			$writer->writeElement('error', 1);
			$writer->writeElement('message', $errorMessage);
		}
		else
		{
			$writer->writeElement('error', 0);
		}
		$writer->endElement();
		$writer->endDocument();
		echo $writer->outputMemory(true);
	}
	
	public function isSecure()
	{
		return false;
	}
}