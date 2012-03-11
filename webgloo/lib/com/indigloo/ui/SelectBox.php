<?php

namespace com\indigloo\ui {

    use com\indigloo\core\Web as Web;
    use com\indigloo\Constants as Constants ;
    
    class SelectBox {
        
        
        static function render($name,$rows,$default=NULL) {
           
            $buffer = '' ;
            $option = '<option value="{code}" {flag}> {display}</option>' ;
            
            foreach($rows as $row) {

				$flag = (!is_null($default) && ($row['code'] == $default))? 'selected' : '' ;
                $str = str_replace(array("{code}","{display}","{flag}") ,
                                   array($row['code'], $row['display'],$flag) , $option);
                $buffer = $buffer.$str ;
                                         
            }
                
            $buffer = '<select name="'.$name.'"> '.$buffer. ' </select>' ;
            return $buffer ;
        }

    }
    
}


?>
