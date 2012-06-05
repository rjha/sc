<?php

namespace com\indigloo\sc\util {

    use com\indigloo\Logger as Logger;
    use com\indigloo\Configuration as Config ;

    class Redis {

        static private $instance = NULL;
        private $connx;

        private function __construct() {
            $this->init();
        }

        private function init() {
            $dsn = Config::getInstance()->get_value("redis.dsn");
            $dsn = "redis://".$dsn ;
            $timeout = Config::getInstance()->get_value("redis.timeout");
            $this->connx = new \redisent\Redis($dsn,$timeout);
        }

        static function getInstance() {
            if (self::$instance == NULL) {
                self::$instance = new \com\indigloo\sc\util\Redis();
            }

            return self::$instance;
        }

        public function connection() {
            return $this->connx;
        }

        public function close() {
            $this->connx->quit();
            self::$instance == NULL;
        }

    }

}
?>
