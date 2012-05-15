<?php 
    include('sc-app.inc');
    include($_SERVER['APP_CLASS_LOADER']);
    include($_SERVER['WEBGLOO_LIB_ROOT'] . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Util as Util;
       
    //report all PHP errors
	error_reporting(-1);
    set_error_handler('offline_error_handler');

    //pre-req : add thumbnail_name varchar(256)
    // alter table sc_media add column thumbnail_name varchar(256);
    //batch size is 50
    //$iter x 50 should be > max(sc_post.id)
    //diagnostic script - After running
    // select id  from sc_post where images_json not  like '%thumbnailName%' 
    // and images_json <> '[]'  order by created_on ;

    $iter = 42;
    $count = 0 ;

    while($count  <= $iter ){
        $mysqli = MySQL\Connection::getInstance()->getHandle();
        $start =  $count*50 + 1 ;
        $end = $start + 49 ;

        printf("processing rows between %d and %d \n",$start,$end);

        $sql = " select id,images_json from sc_post where  (id <= {end}) and (id >= {start} ) ";
        $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);

        $rows = MySQL\Helper::fetchRows($mysqli, $sql);

        foreach($rows as $row) {
            //remove malformed utf-8 characters
            $fjson = iconv('UTF-8', 'UTF-8//IGNORE', $row['images_json']);
            $images = json_decode($fjson);
            //printf("code from json_decode is %d \n",json_last_error());
            $data = array();

            if(!empty($images)) {
                
                foreach($images as $image) {
                    $image->thumbnailName = Util::getThumbnailName($image->originalName);
                    //printf("tname is %s \n",$image->thumbnailName);
                    array_push($data,$image);
                    //update sc_media.thumbnail_name
                    updateMedia($mysqli,$image->id,$image->thumbnailName);
                }
                
                //new mediaVO
                $strMediaVO = json_encode($data);
                //push new mediaVO to sc_post
                updatePost($mysqli,$row['id'],$strMediaVO);
            } else {
                //no images case
                $strMediaVO = '[]' ;
                updatePost($mysqli,$row['id'],$strMediaVO);
            }
        }

        sleep(1);
        $count++ ;
    }
    
 
    function updateMedia($mysqli,$mediaId,$tname) {
      
        $updateSQL = " update sc_media set thumbnail_name = ? where id = ? " ;
        $stmt = $mysqli->prepare($updateSQL);

        if ($stmt) {
            $stmt->bind_param("si", $tname,$mediaId);
            $stmt->execute();
            $stmt->close();
        }
    }

    function updatePost($mysqli,$postId,$strMediaVO) {
      
        $updateSQL = " update sc_post set images_json = ? where id = ? " ;
        $stmt = $mysqli->prepare($updateSQL);

        if ($stmt) {
            $stmt->bind_param("si", $strMediaVO,$postId);
            $stmt->execute();
            $stmt->close();
        }
    }

?>
