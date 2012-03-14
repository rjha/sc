<?php 
	include('sc-app.inc');
	include($_SERVER['APP_CLASS_LOADER']);

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Configuration as Config;
    use \com\indigloo\sc\util\PseudoId as PseudoId;

    $mysqli = MySQL\Connection::getInstance()->getHandle();


    $sql = " select max(id) as max_id  from sc_question " ;
    $row = MySQL\Helper::fetchRow($mysqli,$sql);
    $maxId = $row['max_id'];
    echo "Max id = $maxId \n" ;

    for($i = 1 ; $i <= $maxId; $i++ ){
        $ei = PseudoId::encode($i);
        update($mysqli,$i,$ei);
    }

    function update($mysqli,$x,$ex) {
        $sql = "update sc_question set display_id = ? where id = ? " ;
        $stmt = $mysqli->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("is", $ex,$x);
            $stmt->execute();
            $stmt->close();
        }
    }

?>
