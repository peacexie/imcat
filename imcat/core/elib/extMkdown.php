<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

include DIR_STATIC.'/ximp/class/Parsedown.cls_php'; 

class extMkdown extends \Parsedown{
    
    static function pdext($text,$mode=1){
        if($mode==1){
            $text = preg_replace("/[\r|\n]{1,2} [\-|\>]{1,2} /"," <br> &nbsp; -> ",$text);    
        }
        $text = self::pdorg($text);
        $text = preg_replace("/<li\>(\s+)<p\>([^\n]+)<\/p\>/i",'<li>\2',$text);
        if($mode==1){
            $text = str_replace(array("<li>"),array('<li>● '),$text);
        }
        
        return $text;
    }
    
    static function pdorg($text){
        return self::instance()->parse($text);
    }
    
}

/*
+ uuuu
* tttt
 - xxxx
 > yyyy
--------------------------- 
$text = extMkdown::pdext($text);
$text = 'Hello **Parsedown**!';
$result = extMkdown::instance()->parse($text);
echo $result; # prints: <p>Hello <strong>Parsedown</strong>!</p>
*/
