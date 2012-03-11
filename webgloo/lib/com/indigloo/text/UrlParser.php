<?php
namespace com\indigloo\text{
	
	use com\indigloo\Logger;
	use com\indigloo\Util;
    use com\indigloo\Configuration as Config;
	
    class UrlParser {
		
		/*
		 * copied from http://www.geekality.net/2011/05/12/php-dealing-with-absolute-and-relative-urls/ 
		 * @see also http://publicmind.in/blog/urltoabsolute/ 
		 *
		 */
	
		function createAbsoluteUrl($url, $base) {
			//check input 
			if( ! $url) return NULL;
			if(parse_url($url, PHP_URL_SCHEME) != '') return $url;
			
			// Urls only containing query or anchor
			if($url[0] == '#' || $url[0] == '?') return $base.$url;
			
			// Parse base URL and convert to local variables: $scheme, $host, $path
			extract(parse_url($base));

			// If no path, use /
			if( ! isset($path)) $path = '/';
		 
			// Remove non-directory element from path
			$path = preg_replace('#/[^/]*$#', '', $path);
		 
			// Destroy path if relative url points to root
			if($url[0] == '/') $path = '';
		
			// Dirty absolute URL
			$abs = "$host$path/$url";
		 
			// Replace '//' or '/./' or '/foo/../' with '/'
			$re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
			for($n = 1; $n > 0; $abs = preg_replace($re, '/', $abs, -1, $n)) {}
			
			// Absolute URL is ready!
			return $scheme.'://'.$abs;
		}

		/*
		 * Given a choice between quick & Dirty vs. correct, always do the
		 * quick (dirty!) thing in extract function. We want this to be quick
		 * so do not do DOM parsing or try to do proper word breaking etc!
		 * 
		 */
		
		function extract($url) {
			//clean the Url 
			// last slash is not required 
			// figure out relative vs. full URL here
			// file_get_contents will fail for something like www.3mik.com
			// scheme is required

			$html = file_get_contents($url);
			
			$regex = "/<title>(.+)<\/title>/i";
			preg_match($regex, $html, $matches);
			$title = $matches[1];
			
			$tags = get_meta_tags($url);
			$description = '' ;
			
			
			if(!empty($tags) && array_key_exists('description',$tags)) {
				$description = $tags['description'];
			}
			
			if(empty($description)) {
				/*
				 * forget all the cute heuristics  - does not add anything!
				 * The only sane way would be to run such horrible messes through
				 * an actual renderer like webkit.
				 * @try parsing http://48etikay.com for fun!
				 * 
				 */
				
			}
			
			$srcImages = array();
			
			// fetch images
			$regex = '/<img[^>]*'.'src=[\"|\'](.*)[\"|\']/Ui';
			preg_match_all($regex, $html, $matches, PREG_PATTERN_ORDER);
			$srcImages = $matches[1];
			
			if(sizeof($srcImages) > 10 ) {
				$srcImages = array_splice($srcImages,0,10);
			}
			

			$images = array();

			//create absolute urls
			foreach($srcImages as $srcImage) {
				$absUrl = $this->createAbsoluteUrl($srcImage,$url);
				if(!is_null($absUrl)) {
					array_push($images,$absUrl);
				}
			}

			$data = array('title' => $title,
						  'description' => $description,
						  'images' => $images);
			
			return $data ;
		}
       
    }
}
?>
