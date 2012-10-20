<?php
namespace com\indigloo\sc\util{

    /*
     * class to return namespaced keys for redis and mysql store 
     * @see https://github.com/soveran/nest
     * 
     */
    
    class Nest {

        //redis keys
        static function following($entity,$id) {
            return sprintf("sc:%s:%s:following",$entity,$id);
        }

        static function followers($entity,$id) {
            return sprintf("sc:%s:%s:followers",$entity,$id);
        }

        static function activities($entity,$id) {
            return sprintf("sc:%s:%s:activities",$entity,$id);
        }

        static function feeds($entity,$id) {
            return sprintf("sc:%s:%s:feeds",$entity,$id);
        }

        static function subscribers($entity,$id) {
            return sprintf("sc:%s:%s:subscribers",$entity,$id);
        }

        static function jobs() {
            return "sc:global:jobs" ;
        }

        static function jobId() {
            return "sc:global:nextJobId" ;
        }

        static function queue() {
            return "sc:global:queue:new" ;
        }

        static function global_feeds() {
            return "sc:global:feeds" ;
        }
        
        // DB keys 
        static function fposts() {
            return "set:sys:fposts" ;
        }

        static function fgroups() {
            return "glob:sys:fgroups" ;
        }

        static function ui_category() {
            return "ui:zset:category" ;
        }

        static function preference($entity,$id) {
             return sprintf("glob:%s:%s:preference",$entity,$id);
        }

        static function getTaggedSet($entity,$label) {
            return sprintf("set:sys:%s:%s",$entity,$label) ;
        }

    }

}
?>
