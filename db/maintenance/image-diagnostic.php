#!/usr/bin/php
<?php
    /*
     * image diagnostic script to flag - obvious goof ups
     *
     */

    include('sc-app.inc');
    include(APP_CLASS_LOADER);

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util ;

    set_exception_handler('offline_exception_handler');
    
    $number = 1 ;
    $ch = curl_init();

    function get_http_code($url){

        global $ch ;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return $httpCode ;

    }

    function detect_bad($itemId,$createdOn,$image) {
        global $number ;

        $id = $image->id ;
        $storeName = $image->storeName ;
        $thumbnail = $image->thumbnail ;
        if(Util::contains($storeName,"?")
            || Util::contains($storeName,"&")
            || Util::contains($thumbnail,"?")
            || Util::contains($thumbnail,"&")) {

            $url = "http://media1.3mik.com/".$storeName;
            $code = get_http_code($url);

            printf("%d :: suspected : item %s on %s \n\t image %s \n\t thumbnail %s \n\t code = %s \n\n",
                $number,$itemId,$createdOn,$storeName,$thumbnail,$code);
            sleep(2);
          
            $number++ ;
        }


    }

    function run_diagnostic($mysqli,$flag=false) {

        $sql = "select max(id) as total from sc_post " ;
        $row = MySQL\Helper::fetchRow($mysqli, $sql);
        $total = $row["total"] ;
        $pageSize = 50 ;
        $pages = ceil($total / $pageSize);
        $count = 0 ;

        while($count  <= $pages ){

            $start =  ($count * $pageSize ) + 1 ;
            $end = $start + ($pageSize - 1 ) ;

            $sql = " select pseudo_id,created_on,images_json from sc_post where  (id <= {end}) and (id >= {start} ) ";
            $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);

            $rows = MySQL\Helper::fetchRows($mysqli,$sql);
               
            foreach($rows as $row) {
                
                $images = json_decode($row["images_json"]);
                
                foreach($images as $image) {
                   detect_bad($row["pseudo_id"],$row["created_on"],$image);
                }
            }

            $count++ ;

        }

    }

    $mysqli = MySQL\Connection::getInstance()->getHandle();
    run_diagnostic($mysqli);
    $mysqli->close();


?>
