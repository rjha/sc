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
        sleep(10);
        $html = @file_get_contents($url);

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
        $root = "http://toostep.com/knowledge/a/all/popular/" ;
        $ofile = "toostep.email" ;
        $fhandle = fopen($ofile, "a");

        //rajeev - till 1000
        for($page = 101 ; $page <= 200 ; $page++ ) {
            $pageUrl = $root.$page ;
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

    // uncomment to test 
    // $email = get_profile_email("http://toostep.com/puja-sarkar/profile");
    // printf("email = %s \n",$email);
    // exit ;

    //run

    main_loop();
?>
