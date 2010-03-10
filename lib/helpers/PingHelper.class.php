<?php
class blog_PingHelper
{
	static function authorFromContent($data)
	{
		$authorName = 'Anonymous';
		$matches = array();
		preg_match('|<title>([^<]*?)</title>|is', $data, $matches);
		$title = $matches[1];
		if (f_util_StringUtils::isNotEmpty($title))
		{
			$authorName = str_replace(array('&lt;', '&gt;', '&quot;', '&amp;'), array('<', '>', '"', '&'), $title);
		}
		return $authorName;
	}
	
	static function excerptFromContent($content, $targetURI)
	{
		//Borrowed from wordpress
		$content = str_replace('<!DOC', '<DOC', $content);
		$content = preg_replace( '/[\s\r\n\t]+/', ' ', $content ); // normalize spaces
		$content = preg_replace( "/ <(h1|h2|h3|h4|h5|h6|p|th|td|li|dt|dd|pre|caption|input|textarea|button|body)[^>]*>/", "\n\n", $content );

		$content = strip_tags( $content, '<a>' ); // just keep the tag we need
		$paragraphs = explode( "\n\n", $content);
		
		$pregTargetUri = preg_quote($targetURI, '|');
		foreach ( $paragraphs as $paragraph) 
		{
			if ( strpos($paragraph, $targetURI) !== false ) 
			{ 
				// it exists, but is it a link?
				$context = array();
				preg_match("|<a[^>]+?".$pregTargetUri."[^>]*>([^>]+?)</a>|", $paragraph, $context);

				// If the URL isn't in a link context, keep looking
				if ( f_util_ArrayUtils::isEmpty($context) )
				{
					continue;
				}
					
				$excerpt = preg_replace('|\</?dummymarker\>|', '', $paragraph);

				if ( strlen($context[1]) > 100 )
				{
					$context[1] = f_util_StringUtils::shortenString($context[1], 100);
				}
				
				$marker = '<dummymarker>'.$context[1].'</dummymarker>';  
				$excerpt= str_replace($context[0], $marker, $excerpt); 
				$excerpt = strip_tags($excerpt, '<dummymarker>');
				$excerpt = trim($excerpt);
				$preg_marker = preg_quote($marker, '|');
				$excerpt = preg_replace('|.*?\s(.{0,100}'. $preg_marker . '.{0,100})\s.*|s', '$1', $excerpt);
				$excerpt = strip_tags($excerpt); 
				return $excerpt;
			}
		}
		return null;
	}
}