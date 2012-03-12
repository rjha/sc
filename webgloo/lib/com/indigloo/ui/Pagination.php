<?php

namespace com\indigloo\ui {

    Use \com\indigloo\Url as Url ;
    
    class Pagination {
        
        private $pageNo ;
        private $totalPages ;
		private $qparams ;
		private $pageSize ;

        function __construct($qparams,$total,$pageSize) {

			if(array_key_exists('gpa',$qparams) && array_key_exists('gpb',$qparams)){
				trigger_error('Query params has both gpa and gpb variables',E_USER_ERROR);
			}

			// no global varibale page? assume 1 for scrolling
			$this->pageNo = (array_key_exists('gpage',$qparams)) ? $qparams['gpage'] : 1 ;
			$this->totalPages = ceil($total / $pageSize);
            
            if(empty($this->pageNo) || ($this->pageNo <= 0) || ($this->pageNo > $this->totalPages)) {
                $this->pageNo = 1 ;
			}

			$this->qparams = $qparams ;
			$this->pageSize = $pageSize ;
            
        }

		function isHome() {
			$flag = ($this->pageNo == 1 )? true : false ;
			return $flag;
		}

        function getPageNo(){
            return $this->pageNo ;
        }

		function getPageSize() {
			return $this->pageSize ;
		}

		function getDBParams() {
			$start = NULL;
			$direction = NULL;
		
			if(array_key_exists('gpa',$this->qparams)) {
				$direction = 'after' ;
				$start = $this->qparams['gpa'] ;
			}
			
			if(array_key_exists('gpb',$this->qparams)) {
                $direction = 'before' ;
                $start = $this->qparams['gpb'] ;
			}

			return array('start' => $start , 'direction' => $direction);

		}
        
        function hasNext() {
            if(($this->pageNo < $this->totalPages) && ($this->pageNo <= 20)) {
                return true ;
            } else {
                return false ;
            }
        }
    
        function nextPage() {
            return $this->pageNo + 1 ;
        }
        
        function hasPrevious() {
            if($this->pageNo > 1 ) {
                return true ;
            }else {
                return false ;
            }
    
        }
    
        function previousPage() {
            return $this->pageNo - 1 ;
        }
            
        function render($homeURI,$startId,$endId) {
            
            if(empty($startId) || empty($endId)) {
                return '' ;
            }
            
            //convert to base36
            $startId = base_convert($startId,10,36) ;
            $endId = base_convert($endId,10,36) ;
			printf("<ul class=\"pager\">");
            
            if($this->hasPrevious()){
               
                $bparams = array('gpb' => $startId, 'gpage' => $this->previousPage());
                $q = array_merge($this->qparams,$bparams);
                $ignore = array('gpa');
                
                $previousURI = Url::addQueryParameters($homeURI,$q,$ignore);
                printf("<li> <a href=\"%s\">&larr; Previous</a> </li>",$previousURI);
            }
            
            if($this->hasNext()){
                $nparams = array('gpa' => $endId, 'gpage' => $this->nextPage()) ;
                $q = array_merge($this->qparams,$nparams);
                $ignore = array('gpb');

                $nextURI = Url::addQueryParameters($homeURI,$q,$ignore);
                printf("<li> <a href=\"%s\">Next &rarr;</a> </li>",$nextURI);
            }
            
            printf("</ul>");
        }
    }

}
?>
