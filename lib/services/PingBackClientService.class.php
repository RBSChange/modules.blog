<?php
class blog_PingBackClientService extends BaseService
{
	/**
	 * @var blog_PingBackClientService
	 */
	private static $instance;
	
	/**
	 * @return blog_PingBackClientService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * @param String $url
	 * @param String $source
	 * @param String $target
	 */
	public function ping($url, $source, $target)
	{
		$optionArray = array('prefix' => 'pingback.', 'encoding' => 'utf-8');
		if (defined('OUTGOING_HTTP_PROXY_HOST') && OUTGOING_HTTP_PROXY_HOST && defined('OUTGOING_HTTP_PROXY_PORT') && OUTGOING_HTTP_PROXY_PORT)
		{
			$optionArray['proxy'] = OUTGOING_HTTP_PROXY_HOST . ':' . OUTGOING_HTTP_PROXY_PORT;
		}
		Framework::info('PING ' . $url);
		$client =  new Zend_XmlRpc_Client($url, change_HttpClientService::getInstance()->getNewHttpClient());
		$proxy = $client->getProxy('pingback');
		$result = $proxy->ping($source, $target);
		if (isset($result['flerror']) && $result['flerror'] != 0)
		{
			Framework::warn(__METHOD__ . var_export($result, true));
		}
	}
	
	/**
	 * @param String $url
	 * @return String or null
	 */
	public function getPingbackUrlForUrl($url)
	{
		$client = change_HttpClientService::getInstance()->getNewHttpClient();
		$client->setUri($url);
		$request = $client->request();
		$content = $request->getBody();
		foreach ($client->getHTTPHeaders() as $header)
		{
			$pingbackUrl = $client->getHeader('X-Pingback');
			if (Zend_Uri::check($pingbackUrl))
			{
				return $pingbackUrl;
			}
		}
		$matches = array();
		preg_match('#<link rel="pingback" href="([^"]+)" ?/?>#i', $content, $matches);
		if (count($matches) == 2)
		{
			$pingbackUrl = str_replace(array('&lt;', '&gt;', '&quot;', '&amp;'), array('<', '>', '"', '&'), $matches[1]);
			if (Zend_Uri::check($pingbackUrl))
			{
				return $pingbackUrl;
			}
		}
		return null;
	}
}
