<?php


/*


create database emaildb  character set utf8 collate utf8_general_ci ;
grant all privileges on emaildb.* to 'email'@'localhost' identified by 'email' with grant option;


DROP TABLE IF EXISTS  g_email_table ;
CREATE TABLE  g_email_table (
   id  int NOT NULL AUTO_INCREMENT,
   email varchar(128),
   name varchar(128),
   company varchar(128),
   title varchar(128),
   dup_bit int default 0,
   PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;

alter table g_email_table add constraint unique uniq_email(email);

To dump
mysql> select * from g_email_table into outfile '/tmp/email.01nov' ;

To Load
 LOAD DATA INFILE '/tmp/email.01nov' INTO TABLE g_email_table;

 */



    error_reporting(-1);

    function process_line($line) {
        global $mysqli ;
        global $sql ;

        $line = trim($line) ;
        if(empty($line)) { return ; }

        //break on pipe
        $tokens = explode("|",$line);

        if(empty($tokens)) {
            return ;
        } 

        $first = $tokens[0] ;
        $first = trim($first);
        //break on space now
        $pieces = explode(" ",$first);
        $email = NULL ;

        $size = sizeof($pieces) ;
        switch($size) {
            case 1 :
                $email = $pieces[0] ;
                break ;
            case 2: 
                $email = $pieces[1] ;
                break ;
            default :
                return ;
        }

        if(empty($email)) { return ; }


        if(!empty($email) && (strcmp($email,"__EMAIL__") != 0 )) { 

            $pos = strpos($email, '@');
            if($pos !== false ) {
                //process
                $name = isset($tokens[1]) ? $tokens[1] : "" ;
                $title = isset($tokens[2]) ? $tokens[2] : "" ;
                $company = isset($tokens[3]) ? $tokens[3] : "" ;

                //input check
                if((strlen($email) > 128)  || (strlen($name)> 128)) {
                    $message = sprintf("Too long : email = {%s} , name = {%s} \n",$email,$name);
                    trigger_error($message, E_USER_ERROR);

                } 

                //insert in DB
                $sql = " insert into g_email_table(email,name,title,company) values (?,?,?,?) " ;
                $sql .= " on duplicate key update dup_bit = dup_bit + 1 " ;
                $stmt = $mysqli->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param("ssss", $email,$name,$title,$company);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }

    function get_connector() {
        $host = "127.0.0.1" ;
        $user = "email" ;
        $password = "email";
        $database = "emaildb" ;

        $mysqli = new \mysqli($host,$user,$password,$database);
        if (mysqli_connect_errno ()) {
            trigger_error(mysqli_connect_error(), E_USER_ERROR);
            exit(1);
        }

        return $mysqli ;


    }

    function process_file($fname) {
        $lines = file($fname);
        foreach($lines as $line) {
            process_line($line);
        }
    }

    //files from current directory
    $all_files = scandir(__DIR__);
    $ignore = array(".", "..","email2db.php");
    $ifiles = array_diff($all_files,$ignore);
    $mysqli = get_connector();

    foreach($ifiles as $ifile ){
        process_file($ifile);
    }

    $mysqli->close();


?>
