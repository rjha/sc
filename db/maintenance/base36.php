<?php

        if($argc != 3 || !in_array($argv[1],array('-e','-d'))){
                echo " Usage: php base36.php -[e|d] [token] \n " ;
                exit ;
        }
        
        $opcode = $argv[1] ;
        $token = $argv[2];

        switch($opcode) {
                case '-e' :
                        encode($token);
                        break;
                case '-d' :
                        decode($token);
                        break;
                default:
                        break;  
        }

        function encode($x) {
            printf(" %s encoded to %s \n" ,$x, base_convert($x,10,36));
        }

        function decode($x) {
            printf(" %s decoded to %s \n" ,$x, base_convert($x,36,10));
        }
?>
