<?php
/** 
 * @空操作 404等错误 
 * @author  it动力 http://www.itokit.com 
 */  
class EmptyAction extends Action {  
      
    public function index() {  
        $this->_empty();  
    }  
      
    public function _empty() {  
        //header('HTTP/1.1 404 Not Found');  
		//echo "111";
        $this->display("Index/index");
    }  
}  

	