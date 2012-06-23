<?php 
    include('sc-app.inc');
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\Util as Util;
       
    //report all PHP errors
    error_reporting(-1);
    set_error_handler('offline_error_handler');

    /*
     * v1. 23 june 2012
     *
     * to run $sudo php <script.php> >> script.log 2>&1 
     *
     * what this scripts does?
     *
     * @read all sc_media.rows
     *
     * guess image mime from sc_media.original_name 
     * update sc_media.mime with guessed mime type
     * use application/octet-stream if guess failed (or do not update)
     * get thumbnail name from original Name and update it
     * (unfortunately we were not updating thumbnail names in DB
     *  the issue has been fixed but we need to update old values)
     *
     * mime was not updated for thumbnails before 18 march 2012 and all posts.
     *
     *
     *
     * @read all sc_post.rows
     * get sc_post.row
     * update images_json.images.image.mime using images_json.images.image.originalName 
     *
     */

    
    function updateMediaMime($mysqli,$mediaId,$tname,$mime) {
      
        $updateSQL = " update sc_media set mime = ? , thumbnail_name = ? where id = ? " ;
        $stmt = $mysqli->prepare($updateSQL);

        if ($stmt) {
            $stmt->bind_param("ssi", $mime, $tname,$mediaId);
            $stmt->execute();
            $stmt->close();
        }
    }

    function updatePostMedia($mysqli,$postId,$strMediaVO) {
      
        $updateSQL = " update sc_post set images_json = ? where id = ? " ;
        $stmt = $mysqli->prepare($updateSQL);

        if ($stmt) {
            $stmt->bind_param("si", $strMediaVO,$postId);
            $stmt->execute();
            $stmt->close();
        }
    }

    //no buffer for command line.
    ob_end_flush();
    
    // * start

    $mysqli = MySQL\Connection::getInstance()->getHandle();

    $sql = "select max(id) as total from sc_media " ;
    $row = MySQL\Helper::fetchRow($mysqli, $sql);
    $total = $row["total"] ;
    $pageSize = 50 ;
    $pages = ceil($total / $pageSize);
    $count = 0 ;

    while($count  <= $pages ){

        $start =  ($count * $pageSize ) + 1 ;
        $end = $start + ($pageSize - 1 ) ;

        $sql = " select * from sc_media where  (id <= {end}) and (id >= {start} ) ";
        $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);
        printf("processing row between %d and %d \n",$start,$end);

        foreach($rows as $row) {
            $rowId = $row["id"];
            $name = $row["original_name"];

            $tname = \com\indigloo\Util::getThumbnailName($name);
            if(empty($tname)) {
                $message = sprintf("Bad thumbnail name at id %d \n", $rowId) ;
                trigger_error($message, E_USER_ERROR);
            }

            //guess mime type
            $mime = \com\indigloo\Util::getMimeFromName($name);
            if(empty($mime)) {
                //report it
                printf("Bad mime type for media id %d \n" ,$rowId);
                $mime = "application/octet-stream" ;
            }

            // update sc_media row
            updateMediaMime($mysqli,$rowId,$tname,$mime);
        }

        sleep(1);
        $count++ ;
    }

    $sql = "select count(id) as total from sc_post " ;
    $row = MySQL\Helper::fetchRow($mysqli, $sql);
    $total = $row["total"] ;
    $pageSize = 50 ;
    $pages = ceil($total / $pageSize);
    $count = 0 ;

    while($count  <= $pages ){

        $start =  ($count * $pageSize ) + 1 ;
        $end = $start + ($pageSize - 1 ) ;

        $sql = " select * from sc_post where  (id <= {end}) and (id >= {start} ) ";
        $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);
        printf("processing row between %d and %d \n",$start,$end);

        foreach($rows as $row) {
            //update mime for sc_post.row
            $fjson = $row["images_json"];
            $images = json_decode($fjson);
            $rowId = $row["id"];
            $data = array();

            if(!empty($images)) {
                foreach($images as $image) {
                    $name = $image->originalName ;
                    //guess mime type
                    $mime = \com\indigloo\Util::getMimeFromName($name);
                    if(empty($mime)) {
                        //report it
                        printf("Bad mime type for post id %d \n" ,$rowId);
                        $mime = "application/octet-stream" ;
                    }

                    $image->mime = $mime ;
                    array_push($data,$image);
                }
                
                //new mediaVO
                $strMediaVO = json_encode($data);
                //push new mediaVO to sc_post
                updatePostMedia($mysqli,$rowId,$strMediaVO);
            } 
        }

        sleep(1);
        $count++ ;

    }

    //free resources
    $mysqli->close();



?>
