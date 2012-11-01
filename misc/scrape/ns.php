<?php

    include('sc-app.inc');
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    set_exception_handler('offline_exception_handler');

    function save_lookup($lookup) {
        //serialize and save to file
        $data = serialize($lookup);
        $fhandle = fopen("lookup.dat","w");
        fwrite($fhandle,$data);
        fclose($fhandle);
    }

    function load_lookup() {
        $lookup = array();
        if(file_exists("lookup.dat")) {
            $data = file_get_contents("lookup.dat");
            $lookup = unserialize($data);
        }

        return $lookup ; 
    }

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

        sleep(6);

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
        global $g_debug ;
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
        
        if($g_debug) {
            printf("\n\n ------- dumping POST information -------- \n\n");
            printf("page = %s \n",$url);
            printf("post data = %s \n",$postdata);
        }

        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt ($ch, CURLOPT_POST, 1);

        $result = curl_exec ($ch);
        return $result ;
    }


    function run_on_name($name,$pageNo,$lookup) {
        
        global $g_debug ;

        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64; rv:10.0.7) Gecko/20100101 Firefox/10.0.7 Iceweasel/10.0.7");
        curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);

        $prefix = date("d-M-y");
        $g_ofile = $prefix."/".$name.".email.".time() ;

        if(!file_exists(dirname($g_ofile))) {
                mkdir(dirname($g_ofile), 0755, true);
        }

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

            $pageHtml = do_post($ch,$pageUrl,$name);

            if($g_debug) {
                printf(" \n\n ------ dumping search page having profiles ----- \n\n");
                echo $pageHtml;
            }

            
            $profiles = get_profile_url($pageHtml);
            if(sizeof($profiles) == 0 ) {
                //free resources
                curl_close($ch);
                fclose($fhandle);
                //name processing finished
                return ;
            }
            
            if($g_debug){

                printf(" \n\n ------- dumping extracted profiles from search page ----- \n\n ");
                print_r($profiles);
            }

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
                if($g_debug) {
                    printf(" \n ----- wrote [%s] ----- \n ",$buffer);  
                }
            }

            printf("processed %s - page %s \n",$name,$pageNo);
            $pageNo++ ;
            $lookup[$name] = $pageNo ;
            //save name -page_no to disk
            save_lookup($lookup);
        }

    }

    // -- variables -- 
    $names = file("names.txt");

    // ---- define constants -----------
    $g_acegi_cookie = "c3JpX3NhdXJhYmgyMDAwQHlhaG9vLmNvbToxMzUxNDkwNzg3NzA2OjI3ZTExMzRhYjNiZDMyNzAzZmExNTM0NWZlZDk0NjFj";
    $g_session_cookie = "1419A95DE327C6A7CBDF86475665DA09.s3";
    $g_debug = true ;  

    // --- run ------  
    foreach($names as $name) {
        if(empty($name)) { continue ; }
        $name = trim($name);

        // deserialize the lookup
        $lookup = load_lookup() ;

        $pageNo = 1 ;
        if(array_key_exists($name,$lookup)) {
            $pageNo = $lookup[$name];
            //already processed
            if(intval($pageNo) == -1 ) { continue ; }
        }

        run_on_name($name,$pageNo,$lookup);
        printf("processing over for -  %s \n",$name);

        $lookup[$name] = -1 ;
        save_lookup($lookup);
        sleep(1);
    } 

?>
