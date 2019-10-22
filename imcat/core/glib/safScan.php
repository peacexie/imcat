<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

/**
safScan : xx
  
*/
class safScan{ // extends safBase
    
    static function do($str){
        $tab = self::getTab();
        foreach($tab as $key=>$value){ 
            if(preg_match("/$value/i", $str)){
                safBase::Stop('scanStop');
            }
        }
    }
    
    static function getTab(){ 
        return array( 
            '后门特征->cha88.cn'=>'cha88\.cn', 
            '后门特征->c99shell'=>'c99shell', 
            '后门特征->phpspy'=>'phpspy', 
            '后门特征->Scanners'=>'Scanners', 
            '后门特征->cmd.php'=>'cmd\.php', 
            '后门特征->str_rot13'=>'str_rot13', 
            '后门特征->webshell'=>'webshell', 
            '后门特征->EgY_SpIdEr'=>'EgY_SpIdEr', 
            '后门特征->tools88.com'=>'tools88\.com', 
            '后门特征->SECFORCE'=>'SECFORCE', 
            '后门特征->eval("?>'=>'eval\((\'|")\?>', 
            '可疑代码特征->system('=>'system\(', 
            '可疑代码特征->passthru('=>'passthru\(', 
            '可疑代码特征->shell_exec('=>'shell_exec\(', 
            '可疑代码特征->exec('=>'exec\(', 
            '可疑代码特征->popen('=>'popen\(', 
            '可疑代码特征->proc_open'=>'proc_open', 
            '可疑代码特征->eval($'=>'eval\((\'|"|\s*)\\$', 
            '可疑代码特征->assert($'=>'assert\((\'|"|\s*)\\$', 
            '危险MYSQL代码->returns string soname'=>'returnsstringsoname', 
            '危险MYSQL代码->into outfile'=>'intooutfile', 
            '危险MYSQL代码->load_file'=>'select(\s+)(.*)load_file', 
            '加密后门特征->eval(gzinflate('=>'eval\(gzinflate\(', 
            '加密后门特征->eval(base64_decode('=>'eval\(base64_decode\(', 
            '加密后门特征->eval(gzuncompress('=>'eval\(gzuncompress\(', 
            '加密后门特征->eval(gzdecode('=>'eval\(gzdecode\(', 
            '加密后门特征->eval(str_rot13('=>'eval\(str_rot13\(', 
            '加密后门特征->gzuncompress(base64_decode('=>'gzuncompress\(base64_decode\(', 
            '加密后门特征->base64_decode(gzuncompress('=>'base64_decode\(gzuncompress\(', 
            '一句话后门特征->eval($_'=>'eval\((\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)', 
            '一句话后门特征->assert($_'=>'assert\((\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)', 
            '一句话后门特征->require($_'=>'require\((\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)', 
            '一句话后门特征->require_once($_'=>'require_once\((\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)', 
            '一句话后门特征->include($_'=>'include\((\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)', 
            '一句话后门特征->include_once($_'=>'include_once\((\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)', 
            '一句话后门特征->call_user_func("assert"'=>'call_user_func\(("|\')assert("|\')', 
            '一句话后门特征->call_user_func($_'=>'call_user_func\((\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)', 
            '一句话后门特征->$_POST/GET/REQUEST/COOKIE[?]($_POST/GET/REQUEST/COOKIE[?]'=>'\$_(POST|GET|REQUEST|COOKIE)\[([^\]]+)\]\((\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)\[', 
            '一句话后门特征->echo(file_get_contents($_POST/GET/REQUEST/COOKIE'=>'echo\(file_get_contents\((\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)', 
            '上传后门特征->file_put_contents($_POST/GET/REQUEST/COOKIE,$_POST/GET/REQUEST/COOKIE'=>'file_put_contents\((\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)\[([^\]]+)\],(\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)', 
            '上传后门特征->fputs(fopen("?","w"),$_POST/GET/REQUEST/COOKIE['=>'fputs\(fopen\((.+),(\'|")w(\'|")\),(\'|"|\s*)\\$_(POST|GET|REQUEST|COOKIE)\[', 
            '.htaccess插马特征->SetHandler application/x-httpd-php'=>'SetHandlerapplication\/x-httpd-php', 
            '.htaccess插马特征->php_value auto_prepend_file'=>'php_valueauto_prepend_file', 
            '.htaccess插马特征->php_value auto_append_file'=>'php_valueauto_append_file' 
        ); 
    } 


    // --- End ----------------------------------------
    
}
