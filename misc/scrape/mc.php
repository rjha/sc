<?php

     require_once('phpQuery-onefile.php');

     function get_profile_url($html) {
        $profiles = array();
        $keys = array();

        
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);

        $links = $doc->getElementsByTagName("a");
        
        $length = $links->length ;
        if($length <= 0 ){ 
            return $profiles ;
        }

        for ($i = 0; $i < $length; $i++) {
            //individual link node
            $link = $links->item($i);
            $href = $link->getAttribute("href"); 
            //does the link start with /profile ?
            if(!empty($href) && (strlen($href) > 8)) {

                $prefix = substr($href,1,7);
                $pos = strpos($href,"xg_source");
                $pos2 = strpos($href,"xgac");

                //has profile as well as xg_source
                if((strcasecmp($prefix,"profile") == 0) && ($pos !== false) && ($pos2 === false)) {
                    $key = md5($href);
                    //avoid duplicates!
                    if(!in_array($key,$keys)) {
                        array_push($profiles,"http://www.mediaclubofindia.com".$href);
                        array_push($keys,$key);
                    }
                }
            }
        }

        return $profiles ;

    }


    function get_profile_email($url) {
        global $g_delay ;
        sleep($g_delay);

        @$html = file_get_contents($url);
        phpQuery::newDocument($html);
        $tnode = pq('title');

        $nodes = pq('.module_about_user .xg_module_body dd');
        $buffer = sprintf("\n #profile : %s \n",$tnode->html());  ;

        foreach($nodes as $node) {
            $line = sprintf(" %s \n ",$node->nodeValue);
            $buffer = $buffer.$line ;
        } 

        return $buffer ;

    }

    function fetch_page($url) {
        $result = file_get_contents($url);
        return $result ;
    }


    function fetch_page_email($pageNo) {

        $DOT = "." ;
        $g_ofile = "mc.page".$pageNo.$DOT."email" ;
        $fhandle = fopen($g_ofile, "w");

        //get page
        $pageUrl = "http://www.mediaclubofindia.com/profiles/friend/list?page=".$pageNo ;
        $pageHtml = fetch_page($pageUrl);
        printf("page = %s \n",$pageUrl);

        $profiles = get_profile_url($pageHtml);
        //print_r($profiles); exit ;

        foreach($profiles as $profile) {
            $buffer = get_profile_email($profile);
            fwrite($fhandle,$buffer);
        }

    }

    $g_delay = 3 ;

    $pageNo = 2 ;
    while($pageNo <= 286 ) {
        fetch_page_email($pageNo);
        $pageNo++ ;
    }
    
?>
