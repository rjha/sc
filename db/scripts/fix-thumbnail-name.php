<?php 
    include('sc-app.inc');
    include($_SERVER['APP_CLASS_LOADER']);
    include($_SERVER['WEBGLOO_LIB_ROOT'] . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Util as Util;
       
	error_reporting(-1);
    set_error_handler('offline_error_handler');

    //pre-req : add thumbnail_name varchar(
    // alter table sc_media add column thumbnail_name varchar(256);
    //batch size is 50
    //$iter x 50 should be > max(sc_post.id)

    $iter = 36;
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
            $images = json_decode($row["images_json"]);
            $data = array();

            if(!empty($images)) {
                
                foreach($images as $image) {
                    //create thumbnail_name from original_name 
                    // update sc_post.id with new VO 
                    $sql = " select * from sc_media where id = ".$image->id ;
                    $mediaDBRow = MySQL\Helper::fetchRow($mysqli, $sql);
                    $mediaVO = \com\indigloo\media\Data::create($mediaDBRow);
                    $mediaVO->thumbnailName = Util::getThumbnailName($mediaVO->originalName);
                    array_push($data,$mediaVO);
                    //update sc_media.thumbnail_name
                    updateMedia($mysqli,$image->id,$mediaVO->thumbnailName);
                }
                
                //new mediaVO
                $strMediaVO = json_encode($data);
                //push new mediaVO to sc_post
                updatePost($mysqli,$row['id'],$strMediaVO);
            }
        }

        sleep(2);
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
