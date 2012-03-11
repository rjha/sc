<?php

/**
 *
 * @author rajeevj
 * @see also http://dev.mysql.com/doc/refman/5.0/en/error-messages-server.html
 * 
 */

namespace com\indigloo\mysql {

    use com\indigloo\Logger;
    use com\indigloo\mysql as MySQL;

    class Error {

        static function handle($module, $dbHandle) {

            $errorNo = $dbHandle->errno;
            //error code zero means operation success
            if (empty($errorNo)) {
                return $errorNo;
            }

            $map = array(
                1062 => MySQL\Connection::DUPLICATE_KEY
            );

            $message = $module . ':: DB error no:: ' . $errorNo . ' :: message:: ' . $dbHandle->error;
            
            // errors that we are willing to handle
            if (array_key_exists($errorNo, $map)) {
                Logger::getInstance()->error($message);
                //get Gloo DB code for this error
                $code = $map[$errorNo];
                return $code;
            } else {
                //crash and burn errors
                trigger_error($message, E_USER_ERROR);
                exit(1);
            }
        }

    }

}
?>
