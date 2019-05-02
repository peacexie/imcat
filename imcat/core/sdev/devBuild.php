<?php
namespace imcat;

// ...类

class devBuild{    

    // 翻译一个系统语言包文件/ devBuild::trsfp('kvphp/flow-fr', 'fr');
    static function trsfp($fp, $to, $from='cn'){
        $tab = [
            'cn'=>'zh', //'en'=>'en', // 汉,英
            'fr'=>'fra', 'es'=>'spa', //'ru'=>'ru', // 法,西,俄
            //'de'=>'de', 'jp'=>'jp', // 德,日
            'kr'=>'kor', 'ar'=>'ara', // 韩,阿
        ];
        $ff = DIR_IMCAT."/lang/$fp.php"; // 'ptinc/aflow-cn.php';
        $bk = "{$ff}-bk"; if(file_exists($bk)) return;
        $from = isset($tab[$from]) ? $tab[$from] : $from;
        $to = isset($tab[$to]) ? $tab[$to] : $to;
        $type = strstr($fp,'ptinc/') ? (strpos($fp,'aflow-') ? 'html' : 'line') : '';
        $dsave = self::trans($ff, $to, $type, $from);
        copy($ff, $bk);
        comFiles::put($ff, $dsave);
        return $fp;
    }

    // type: (null), html(翻译html的节点), line(按行翻译)
    static function trans($fp, $to, $type='', $from='ch', $re=1){
        $dstr = $dorg = comFiles::get($fp);
        $data = []; // 提取中文数组
        if($type=='line'){
            $dstr = preg_replace("/\<([^>|\n]+)\>/", "\n", $dstr);
            $arr = explode("\n", $dstr);
            $data = self::trarr($arr, 1);
        }elseif($type=='html'){
            preg_match_all("/\>([^<]+)\</", $dstr, $arr);
            if(!empty($arr[1])){
                $data = self::trarr($arr[1], 1);
            }
        }else{
            $arr = include($fp);
            foreach($arr as $vals){
                if(is_array($vals)){
                    foreach($vals as $val){ $data[] = $val; }
                }else{ $data[] = $vals; }
            }
        } //return $data; die();
        $trans = self::trapi($data, $to, $from); // 得到英文翻译
        $dsave = self::trrep($trans, $dorg, $type); // 替换翻译
        if($re) return $dsave;
        comFiles::put("$fp-$to", $dsave);
    }
    // 翻译替换
    static function trrep($trans, $dorg, $type=''){
        $data = $dorg;
        if(!$type){
            foreach($trans['from'] as $tk=>$tv) {
                $val = $trans['to'][$tk];
                $vf = ["'$tv'", '"'.$tv.'"'];
                $vt = ["'$val'", '"'.$val.'"'];
                $data = str_replace($vf, $vt, $data);
            }
        }else{
            $data = str_replace($trans['from'], $trans['to'], $data);
        }
        return $data;
    }
    // 对接一次翻译API
    static function trapi1(&$trans, $str, $to, $from='ch'){
        $res = aisTrans::main($str, $from, $to);
        if(!empty($res['trans_result'])){
            foreach($res['trans_result'] as $itms){
                $trans['from'][] = $itms['src'];
                $trans['to'][] = str_replace("'", '`', $itms['dst']);
            }
        }
    }
    // 翻译所有数组(分批)
    static function trapi($data, $to, $from='ch'){
        $str = ''; $cnt = 0; 
        $trans = [];
        foreach($data as $val){
            $ilen = mb_strlen($str);
            if($cnt+$ilen>1200){
                self::trapi1($trans, $str, $to, $from);
                $str = $val; 
                $cnt = $ilen;
            }else{
                $str .= ($str?"\n":'')."$val"; 
                $cnt += $ilen;
            }
        } //return $trans; die();
        if($cnt){
            self::trapi1($trans, $str, $to, $from);
        }
        return $trans;
    }
    // 数组转化（翻译用）
    static function trarr($arr, $trm2=0){
        $data = $res = [];
        foreach($arr as $val){
            $ival = trim($val);
            if($trm2){
                $ival = preg_replace("/^([\x20-\x7f]+)/i", "", $ival);
                $ival = preg_replace("/([\x20-\x7f]+)$/i", "", $ival);
                $ival = trim($ival); 
            }
            if($ival){
                $ilen = mb_strlen($ival);
                $ikey = $ilen>35 ? 36 : $ilen;
                if(!isset($data[$ikey]) || !in_array($ival,$data[$ikey])){
                    $data[$ikey][] = $ival;
                }
            }
        }
        for($i=36;$i>0;$i--){ if(isset($data[$i])){
            foreach($data[$i] as $val) {
                $res[] = $val;
            }
        } }
        return $res;
    }

    static function clang($org, $obj){ 
        if(empty($org)||empty($obj)) return 'Error';
        $lists = comFiles::listScan(DIR_IMCAT.'/lang');
        foreach ($lists as $file=>$itm) {
            if(strpos($file, "-$org.php")){
                $ofile = DIR_IMCAT.'/lang/'.str_replace("-$org.php","-$obj.php",$file);
                copy(DIR_IMCAT."/lang/$file",$ofile);
            }
        }
        return 'OK';
    }

    // 创建应用 
    static function create($dir, $front, $mod){ 
        if(strlen(basStr::filKey($dir,''))<3 || strlen(basStr::filKey($front,''))<3){
            return basLang::show('devapp_dferr');
        } 
        if(is_numeric($dir) || is_numeric($front)){
            return basLang::show('devapp_dfnum');
        }
        $exa = array('demo','front','home','info');
        if(in_array($dir,$exa) || in_array($front,$exa)){
            return basLang::show('devapp_dfues');    
        }
        $vopcfg = glbConfig::read('vopcfg','sy'); 
        $groups = glbConfig::read('groups'); 
        if(isset($vopcfg['tpl'][$dir]) || is_dir(DIR_VIEWS."/$dir")){
            return basLang::show('devapp_dfext');
        }
        if(empty($groups[$mod]['pid']) || $groups[$mod]['pid']!='docs'){
            return basLang::show('devapp_dataerr');
        }
        self::cdir(DIR_VIEWS."/demo", DIR_VIEWS."/$dir", $mod);
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
        $title = basReq::val('title',"{$dir}App");
        // front
        $data = comFiles::get(DIR_ROOT.'/run/demo.php');
        $data = str_replace(array("'demo'","__DIR__.'/_init.php'"),array("'$dir'","__DIR__.'/root/run/_init.php'"),$data);
        comFiles::put(DIR_PROJ."/$front.php", $data);
        // vopcfg
        $data = comFiles::get(DIR_ROOT.'/cfgs/sycfg/sy_vopcfg.php');
        $flag = "\$_sy_vopcfg['tpl'] = array(".PHP_EOL;
        $icfg = "    '$dir' => array(".PHP_EOL."        '$title',".PHP_EOL."        '/$front.php'".PHP_EOL."    ),".PHP_EOL.'    ';
        $data = preg_replace("/[$]_sy_vopcfg\[\'tpl\'\]\s{0,4}\=\s{0,4}array\(\s{0,4}/is", $flag.$icfg, $data);
        comFiles::put(DIR_ROOT.'/cfgs/sycfg/sy_vopcfg.php', $data);
    }

    // modOpt
    static function modOpt($mod){ 
        $_groups = glbConfig::read('groups'); 
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

