<?php

namespace com\indigloo\media {

    use com\indigloo\Configuration as Config;
    use com\indigloo\Logger;
    
    class FileStore {

        
        function __construct() {
        }

        function __destruct() {
        
        }

        static function getHashedName($name) {
            $token = $name.date(DATE_RFC822);
            $storeName = substr(md5($token), rand(1, 15), 16).rand(1,4096);
            $pos = strrpos($name, '.');

            if ($pos != false) {
                //separate filename and extension
                $extension = substr($name, $pos + 1);
                $storeName =  $storeName. '.' . $extension;
            } 

            return $storeName ;
        }

        function persist($prefix,$name,$sBlobData,$headers=array()) {

            $storeName = self::getHashedName($name) ;
            $storeName =  $prefix.$storeName ;
            
            $fp = NULL;
            //system.upload.path has a trailing slash
            $path = Config::getInstance()->get_value('system.upload.path').$storeName;
            
            if(!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            
            if(Config::getInstance()->is_debug()){
                Logger::getInstance()->debug(" file name = $name");
                Logger::getInstance()->debug(" storage path is => $path ");
            }
            
            //open file in write mode
            $fp = fopen($path, 'w');
            fwrite($fp, $sBlobData);
            fclose($fp);   
            
            return $storeName;
        }

    }
}

?>
