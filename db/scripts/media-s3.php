<?php 
    include('sc-app.inc');
    include($_SERVER['APP_CLASS_LOADER']);
    include($_SERVER['WEBGLOO_LIB_ROOT'] . '/com/indigloo/error.inc');
    include($_SERVER['WEBGLOO_LIB_ROOT'] . '/ext/S3.php');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\media\ImageUpload as ImageUpload;
    use \com\indigloo\media\FilePipe as FilePipe;
       
	error_reporting(-1);

    $prefix = sprintf("%s/",date('Y/m/d')) ;
    $pipe = new \com\indigloo\media\FilePipe();
    $uploader = new com\indigloo\media\ImageUpload($pipe);

    $count = 0 ;

    while(1) {
        //get media in batch size of 20 
        $sql = "select  id,original_name,stored_name from sc_media where store = 'local' order by id desc limit 10";
        $mysqli = MySQL\Connection::getInstance()->getHandle();
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);

        if(sizeof($rows) == 0 ) {
            printf("No more local rows \n");
            break ;
        }

        //we have rows to process
        foreach($rows as $row) {
            printf("processing row id %d \n" ,$row['id']);

            //write a pipe that can return fileData 
            $abspath = "/home/rjha/web/upload/" .$row["stored_name"];
            $uploader->process($prefix,$abspath);
            $errors = $uploader->getErrors() ;

            if (sizeof($errors) > 0) {
                print_r($errors);
                exit ;
            } else {
                $mediaVO = $uploader->getMediaData();
                //print_r($mediaVO); 
                updateMedia($mysqli,$row["id"],$mediaVO);
            }

            sleep(3);
        } //loop

        sleep(1);
        $count++ ;
        printf("loop iteration number %d \n" ,$count);
  
    }

    

   
    function updateMedia($mysqli,$mediaId,$mediaVO) {
      
        //update m.thumbnail,m.stored_name,m.bucket,m.store
        $updateSQL = " update sc_media set thumbnail = ?,stored_name = ?,bucket = ?, store = ? where id = ? " ;
        $stmt = $mysqli->prepare($updateSQL);
        $store = "s3" ;

        if ($stmt) {
            $stmt->bind_param("ssssi",
                        $mediaVO->thumbnail,
                        $mediaVO->storeName,
                        $mediaVO->bucket, 
                        $store,
                        $mediaId);
                      
                $stmt->execute();
                $stmt->close();
        }
    }

?>
