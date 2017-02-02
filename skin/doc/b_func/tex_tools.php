<?php
/*
单个模板扩展函数
*/ 
class tex_tools{ //extends tex_base
    
    #protected $prop1 = array();
    
    static function rndIP(){
        $s = rand(3,254);
        for($a=0;$a<3;$a++){
            $s .= ".".rand(10,240);    
        }
        return $s;    
    }

}
