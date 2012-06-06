<?php

namespace com\indigloo\sc\html\feed {

    use \com\indigloo\sc\Constants as AppConstants;
    
    class ProcessorFactory {

        static function get($type) {
            $processor = NULL;

            switch ($type) {
                case AppConstants::FOLLOW_FEED :
                    $processor = new Processor();
                    break;

                default :
                    $processor = new PostProcessor();
                    break;
            }

            return $processor;
        }

    }

}
?>
