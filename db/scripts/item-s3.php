<?php 
    include('sc-app.inc');
    include($_SERVER['APP_CLASS_LOADER']);
    include($_SERVER['WEBGLOO_LIB_ROOT'] . '/com/indigloo/error.inc');
    include($_SERVER['WEBGLOO_LIB_ROOT'] . '/ext/S3.php');

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Configuration as Config;
       
	error_reporting(-1);
    //printf("Make backup of DB first \n");
    //exit ;

    $iter = 40;
    $count = 0 ;

    while($count  <= $iter ){
        $start =  $count*20 ;
        $end = $start + 20 ;

        $sql = " select id,images_json from sc_question where  (id <= {end}) and (id >= {start} ) ";
        $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);
        //printf("%s \n ", $sql);
        //$count++ ;
        //continue ;

        $mysqli = MySQL\Connection::getInstance()->getHandle();
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);


        foreach($rows as $row) {
            printf("processing row id %d \n" ,$row['id']);
            $images = json_decode($row["images_json"]);
            $data = array();

            foreach($images as $image) {
                printf("processing image id %d \n",$image->id);
                //load media DB row
                // update sc_question.id with new media DB Row
                $sql = " select * from sc_media where id = ".$image->id ;
                $mediaDBRow = MySQL\Helper::fetchRow($mysqli, $sql);
                $mediaVO = \com\indigloo\media\Data::create($mediaDBRow);
                array_push($data,$mediaVO);
            }
            
            $strMediaVO = json_encode($data);
            updateItem($mysqli,$row['id'],$strMediaVO);
            sleep(1);
        }

        $count++ ;
    }
    

      function updateItem($mysqli,$questionId,$strMediaVO) {
      
        $updateSQL = " update sc_question set images_json = ? where id = ? " ;
        $stmt = $mysqli->prepare($updateSQL);

        if ($stmt) {
            $stmt->bind_param("si", $strMediaVO,$questionId);
            $stmt->execute();
            $stmt->close();
        }
    }

?>
