#!/usr/bin/php
<?php
    include('sc-app.inc');
    include(APP_CLASS_LOADER);

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util ;

    set_exception_handler('offline_exception_handler');

    function detect_bad_utf8($mysqli,$flag=false) {

        $sql = "select max(id) as total from sc_post " ;
        $row = MySQL\Helper::fetchRow($mysqli, $sql);
        $total = $row["total"] ;
        $pageSize = 50 ;
        $pages = ceil($total / $pageSize);
        $count = 0 ;

        while($count  <= $pages ){

            $start =  ($count * $pageSize ) + 1 ;
            $end = $start + ($pageSize - 1 ) ;

            $sql = " select pseudo_id,title,description from sc_post where  (id <= {end}) and (id >= {start} ) ";
            $sql = str_replace(array("{end}", "{start}"),array( 0 => $end, 1=> $start),$sql);

            $rows = MySQL\Helper::fetchRows($mysqli,$sql);
            printf("processing row between %d and %d \n",$start,$end);

            foreach($rows as $row) {
                $description = $row['description'];
                if(!Util::isUtf8($description)) {
                    printf("Bad utf-8 for item - %s \n", $row['pseudo_id']);
                    if($flag) {
                        $clean_description = Util::filterBadUtf8($description);
                        $clean_title =  Util::filterBadUtf8($row['title']);
                        update_clean_utf8($mysqli,
                            $row['pseudo_id'],
                            $clean_title,
                            $clean_description);
                    }
                }
            }

            sleep(1);
            $count++ ;

        }

    }

    function update_clean_utf8($mysqli,$itemId,$title,$description) {
        $updateSQL = " update sc_post set title = ? , description = ? where pseudo_id = ? " ;
        $stmt = $mysqli->prepare($updateSQL);

        if ($stmt) {
            $stmt->bind_param("ssi", $description,$title,$itemId);
            $stmt->execute();
            $stmt->close();
        }

    }

    $mysqli = MySQL\Connection::getInstance()->getHandle();
    detect_bad_utf8($mysqli,true);
    
    //close resources
    $mysqli->close();
?>
