<?php

     function get_profile_url($html) {
        $profiles = array();
        $keys = array();

        
        $doc = new \DOMDocument();
        $html = "<html><head> <title> test </title> </head> <body> ".$html."</body> </html>";
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
            //does the link href end in profile?
            $pos = strrpos($href,"/");
            if($pos !== false ) {
                $token = substr($href,$pos+1);
                if(strcasecmp($token,"profile") == 0 ) {
                    $key = md5($href);
                    //avoid duplicates!
                    if(!in_array($key,$keys)) {
                        array_push($profiles,$href);
                        array_push($keys,$key);
                    }
                }
            }
        }

        return $profiles ;

    }

    function get_profile_email($url) {
        global $g_acegi_cookie ;
        global $g_session_cookie ;
        global $g_delay ;

        sleep($g_delay);

        // Create a stream
        // get cookie values using wget commands

        $cookie = sprintf("Cookie: ACEGI_SECURITY_HASHED_REMEMBER_ME_COOKIE=%s; JSESSIONID=%s\r\n",
                $g_acegi_cookie,
                $g_session_cookie);

        $opts = array('http'=>array('method' => "GET", 'header' => "Accept-language: en\r\n" .$cookie));
        $context = stream_context_create($opts);
        @$html = file_get_contents($url,false,$context);


        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $emailNode = $doc->getElementById("email1__ID__");

        $nameNode = $doc->getElementById("name1__ID__");
        $titleNode = $doc->getElementById("title1__ID__");
        $companyNode = $doc->getElementById("company1__ID__");

        if(!is_null($emailNode)) {
            $data = array();

            $data["email"] = $emailNode->nodeValue;
            $data["name"] = is_null($nameNode) ? "" : $nameNode->nodeValue;
            $data["title"] = is_null($titleNode)? "" : $titleNode->nodeValue;
            $data["company"] = is_null($companyNode) ? "" : $companyNode->nodeValue;

            return $data ;
        }

        return NULL ;
    }

    function do_post($ch,$url,$token) {
        global $g_acegi_cookie ;
        global $g_session_cookie ;


        // get cookie values using wget commands
        $cookie = sprintf("Cookie: ACEGI_SECURITY_HASHED_REMEMBER_ME_COOKIE=%s; JSESSIONID=%s\r\n",
                $g_acegi_cookie,
                $g_session_cookie);


        $data = array(
            "advanceSearch" => "1" ,
            "randomBoostSlab" => "-1",
            "searchString" => $token,
            "allCriteria" => "0",
            "searchExpert" => "0");

        $postdata = http_build_query($data);
        $length = strlen($postdata);

        $headers = array(
            "X-Requested-With: XMLHttpRequest",
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
            "Content-Length: ".$length,
            $cookie
           );
        

        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt ($ch, CURLOPT_POST, 1);

        $result = curl_exec ($ch);
        return $result ;
    }


    function run_on_name($ch,$name,$pageNo) {
        global $g_debug ;

        $DOT = "." ;
        $g_ofile = urlencode($name).$DOT.time().$DOT."email" ;
        $fhandle = fopen($g_ofile, "w");

        while(1) {

            $dtime = time()."999" ;

            $params = array(
            "pageNo" => $pageNo,
            "type" => "page",
            "archive" => "0" ,
            "d" => $dtime) ;

            $qstring = http_build_query($params);
            $pageUrl = "http://toostep.com/searchUserAjax.html?".$qstring ;

            if($g_debug) {
                print_r($params);
                printf("page = %s \n",$pageUrl);
            }

            $pageHtml = do_post($ch,$pageUrl,$name);

            if($g_debug) {
                echo $pageHtml;
            }
            
            $profiles = get_profile_url($pageHtml);
            if(sizeof($profiles) == 0 ) {
                fclose($fhandle);
                //name processing finished
                return ;
            }
            
            //print_r($profiles); exit ;

            //use profiles to get emails
            foreach($profiles as $profile) {
                $data = get_profile_email($profile);
                $buffer = NULL ;

                if(empty($data) || is_null($data)) {
                    $buffer = sprintf("__NO_DATA__ %s \n",$profile) ;
                } else {
                    $buffer = sprintf("__DATA__ %s|%s|%s|%s \n",
                        $data["email"],
                        $data["name"],
                        $data["title"],
                        $data["company"]) ;
                }

                fwrite($fhandle,$buffer);
            }

            $pageNo++ ;
        }

    }

    // 1-100 seo
    // 1-100 sales
    // 1-400 marketing
    // air hostess 1-100
    // fashion 1-100
    // designer 1-100
    // secretary 1-100
    // front office 1-100
    // export 1-300
    // hotel management
    
    $tokens = array(
        "manager" => 1,
        "zeenat" => 1); 

    // ---- define constants -----------
    $g_acegi_cookie = "c3JpX3NhdXJhYmgyMDAwQHlhaG9vLmNvbToxMzUyMjk2MjYxOTY4OmNjN2IxNjZkNTFlOThlOTg4YjJkNTkwNDFkZTE3ZDFh";
    $g_session_cookie = "580B7BE80DF49A6F7444B7C8230F7D77.s2";




    $g_debug = false ;
    //delay in seconds
    $g_delay = 4 ;

     
    // --- run ------  

    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64; rv:10.0.7) Gecko/20100101 Firefox/10.0.7 Iceweasel/10.0.7");
    curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
    
    //actual search page results - what you see on UI
    foreach($tokens as $name => $pageNo) {
        printf("process name %s \n",$name);
        run_on_name($ch,$name,$pageNo);
        printf("processed name %s \n",$name);

    } 

    //free resources
    curl_close($ch);

?>
