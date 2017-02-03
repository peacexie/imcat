<?php

// ...类

class devBuild{    

    static function clang($org, $obj){ 
        if(empty($org)||empty($obj)) return 'Error';
        $lists = comFiles::listScan(DIR_CODE.'/lang');
        foreach ($lists as $file=>$itm) {
            if(strpos($file, "-$org.php")){
                $ofile = DIR_CODE.'/lang/'.str_replace("-$org.php","-$obj.php",$file);
                copy(DIR_CODE."/lang/$file",$ofile);
            }
        }
        return 'OK';
    }

    // 创建应用 
    static function create($dir, $front, $mod){ 
        if(strlen(basStr::filKey($dir,''))<3 || strlen(basStr::filKey($front,''))<3){
            return lang('devapp_dferr');
        } 
        if(is_numeric($dir) || is_numeric($front)){
            return lang('devapp_dfnum');
        }
        $exa = array('demodir','front','home','info');
        if(in_array($dir,$exa) || in_array($front,$exa)){
            return lang('devapp_dfues');    
        }
        $vopfmt = read('vopfmt','ex'); 
        $groups = read('groups'); 
        if(isset($vopfmt['tpl'][$dir]) || is_dir(DIR_SKIN."/$dir")){
            return lang('devapp_dfext');
        }
        if(empty($groups[$mod]['pid']) || $groups[$mod]['pid']!='docs'){
            return lang('devapp_dataerr');
        }
        self::cdir(DIR_SKIN."/demodir", DIR_SKIN."/$dir", $mod);
        self::cfiles($dir, $front, $mod);
        return 'OK'; //"<input type='text' value='dir=$dir,front=$front,mod=$mod' class='disc'>";
    }
    
    // 复制目录
    static function cdir($src, $dst, $mod) {  // 原目录，复制到的目录
        $dir = opendir($src);
        @mkdir($dst); //(news)}">news<
        $aorg = array("news_",   "_news",   "news-",   "(news)", ">news<", ",news]");
        $aobj = array("{$mod}_", "_{$mod}", "{$mod}-", "($mod)", ">$mod<", ",$mod]");
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src.'/'.$file) ) {
                    self::cdir($src.'/'.$file, $dst.'/'.$file, $mod);
                }else{
                    $fobj = str_replace($aorg,$aobj,$file);
                    $data = comFiles::get($src.'/'.$file);
                    $data = str_replace($aorg,$aobj,$data);
                    comFiles::put($dst.'/'.$fobj, $data);
                }
            }
        }
        closedir($dir);
    }

    // 修改文件
    static function cfiles($dir, $front, $mod){ 
        $title = req('title',"{$dir}App");
        // front
        $data = comFiles::get(DIR_ROOT.'/run/front.php');
        $data = str_replace(array("'demodir'","dirname(__FILE__).'/_init.php'"),array("'$dir'","dirname(__FILE__).'/root/run/_init.php'"),$data);
        comFiles::put(DIR_PROJ."/$front.php", $data);
        // vopfmt
        $data = comFiles::get(DIR_CODE.'/cfgs/excfg/ex_vopfmt.php');
        $flag = "\$_ex_vopfmt['tpl'] = array(".PHP_EOL;
        $icfg = "    '$dir' => array(".PHP_EOL."        '$title',".PHP_EOL."        '/$front.php'".PHP_EOL."    ),".PHP_EOL.'    ';
        $data = preg_replace("/[$]_ex_vopfmt\[\'tpl\'\]\s{0,4}\=\s{0,4}array\(\s{0,4}/is", $flag.$icfg, $data);
        comFiles::put(DIR_CODE.'/cfgs/excfg/ex_vopfmt.php', $data);
    }

    // modOpt
    static function modOpt($mod){ 
        $_groups = read('groups'); 
        $ops = '';
        foreach($_groups as $km=>$kv){
            if($kv['pid']=='docs'){
                $selected = $km==$mod ? 'selected' : '';
                $ops .= "\n<option value='$km' $selected>[$km]{$kv['title']}</option>";
            }
        }
        return $ops;
    }

}

