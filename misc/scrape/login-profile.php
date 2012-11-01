<?php


     
    function get_profile_url($url) {
        sleep(15);
        $profiles = array();
        $keys = array();

        //find profile url on this page
        $html = @file_get_contents($url);
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);

        $links = $doc->getElementsByTagName("a");
        $length = $links->length ;
        if($length <= 0 ){ 
            return ;
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

        sleep(10);

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

    
    function main_loop() {
        global $g_root ; 
        global $g_ofile ;

        $fhandle = fopen($g_ofile, "w");

        //rajeev - till 1000
        for($page = 201 ; $page <= 300 ; $page++ ) {
            $pageUrl = $g_root.$page ;
            $profiles = get_profile_url($pageUrl);

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
        }

        //close resources
        fclose($fhandle);
    }


    // constants
    $g_root = "http://toostep.com/knowledge/a/all/popular/" ; 
    $g_ofile = "profile.email.".time() ;
    // copy cookies from browser session
    // or get from wget 
    // wget --save-cookies wcookies.txt --keep-session-cookies --post-data "j_username=sri_saurabh2000@yahoo.com&j_password=Jantu211" http://toostep.com/j_security_check
    //
    $g_acegi_cookie = "c3JpX3NhdXJhYmgyMDAwQHlhaG9vLmNvbToxMzQ5Njc4Mzg4MjY5OjYyODdjZTljOTVkZTI1Y2NiNzIwODBjZGMwZWRmMDdi";
    $g_session_cookie = "6A3DD831C32E3EDD4B713C7AF8D1BB5A.s2" ;


    main_loop();


?>
