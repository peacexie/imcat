<?php
namespace imcat;

// ... 代码导出类（申请著作权）

class devCoder{    

    // 主入口
    static function expMain($cfgs, $file1='', $file2=''){
        $flag1 = $flag2 = 0; 
        $str1 = $str2 = "";
        $lines = 0; echo "<pre>";
        foreach ($cfgs as $fd=>$cfg) { 
            $fd = str_replace(array(':01',':02',':03'), '', $fd); 
            $list = self::expList1($fd,$cfg); 
            foreach ($list as $file) {
                $row = self::expInfos($file);
                if(strpos($file,$file2)) $flag2 = 1;
                if(!$flag1){ $str1 = $str1 . $row[1]; }
                if($flag2){ $str2 = $str2 . $row[1]; }
                if(strpos($file,$file1)) $flag1 = 1;
                echo $row[2];
                $lines += $row[0];
            }
        } echo "</pre>";
        $fpext = defined('EXP_HLIGHT') ? '.htm' : '.txt';
        $fpout = str_replace('.php', '', basename($_SERVER['SCRIPT_NAME']));
        $cset = defined('EXP_HLIGHT') ? '<meta charset="utf-8">' : '';
        comFiles::put("./output/{$fpout}1$fpext", $cset.$str1);
        comFiles::put("./output/{$fpout}2$fpext", $cset.$str2);
        dump($lines);
    }

    // 得到文件列表
    static function expList1($k, $cfg){
        global $sdirs, $sfiles, $sfinstr;
        $sub = $k ? $k : '';
        $arr = $res = array();
        if(is_array($cfg)){
            $arr = $cfg;
        }elseif($cfg=='files'){
            $arr = comFiles::listDir(EXP_ROOT.$sub, 'file');
        }else{
            $arr = comFiles::listScan(EXP_ROOT.$sub);
        }
        foreach ($arr as $file=>$cfg) {
            $fp = is_array($cfg) ? $file : $cfg; 
            $skip = 0;
            if(!empty($sdirs)){
                foreach ($sdirs as $flg) {
                    if(strpos("$sub/$fp","/$flg/")){ $skip = 1; break; }
                }
            }
            if(!empty($sfiles)){
                foreach ($sfiles as $flg) {
                    if(strpos("$sub/$fp","/$flg")){ $skip = 1; break; }
                }
            }
            if(!empty($sfinstr)){
                foreach ($sfinstr as $flg) {
                    if(strpos("$sub/$fp",$flg)){ $skip = 1; break; }
                }
            }
            if(!$skip){
                $res[] = "$sub/$fp";
            }
        }
        return $res;
    }

    // 得到一个文件信息
    static function expInfos($fp){
        global $rep1,$rep2;
        $fpreal = file_exists(EXP_ROOT."$fp-cdemo") ? "$fp-cdemo" : $fp;
        $data = comFiles::get(EXP_ROOT.$fpreal);
        $data = basStr::filNotes($data, 0);
        if(!$data) return [0, '', ''];
        if(basStr::isConv($data)){
            $data = comConvert::autoCSet($data, 'gb2312');
        } 
        $arr = file(EXP_ROOT.$fp); // EXP_HLIGHT
        $line = count($arr);
        if(defined('EXP_HLIGHT') && strpos($fp, '.php')){
            $head = "\n<b>### file: $fp </b><br/>\n"; 
            $data = highlight_string($data, 1);
            $data = str_replace(array('<br />'), array("<br />\n"), $data);
        }else{
            $head = "\r\n\r\n### file: $fp\r\n\r\n"; 
            $data = $data; //nl2br($data);
        }
        $data = (defined('EXP_NOHEAD') ? "\r\n\r\n" : $head).$data;
        // msg
        $sfp = str_replace($rep1, $rep2, $fp);
        $sfp = strlen($sfp)>62 ? '...'.substr($sfp,-60) : $sfp; 
        $spad = str_pad($sfp.' ',68,"-");
        $sline = str_pad($line,6," ");
        $kbs = round(filesize(EXP_ROOT.$fp)/1024,2);
        $skbs = str_pad($kbs, 6, " ").'KB';
        $msg = "$spad lines: $sline <a href='?fp=$fp' target='_blank'>{$skbs}</a><br>";
        return [$line, $data, $msg];
    }

    // static-analyse : 静态文件分析 : 扫描obj_dir中的资源,是否在from_dirs中使用
    static function stScan($obj_dir, $obj_bak, $obj_fix, $from_dirs){
        $act = req('act');
        $res = $froms = [];
        // 
        $ftabs = comFiles::listDir(BDIR.$obj_dir, 'file');
        $files = array_keys($ftabs); //dump($files);
        // 
        foreach ($from_dirs as $dir) {
            $ftabs = comFiles::listDir(BDIR.$dir, 'file');
            foreach ($ftabs as $file=>$row) {
                $fp = "$dir/$file";
                $froms[$fp] = comFiles::get(BDIR.$fp);
            }
        } //dump(array_keys($froms));
        // 
        foreach ($files as $file) {
            $fchk = "$obj_fix/$file";
            $fcnt = 0;
            foreach ($froms as $fp=>$fdata) {
                if(strpos($fdata,$fchk)>0){
                    $fcnt++; continue;
                }
            }
            $fk = str_pad($file.' ',24,"-");
            $res[$fk] = "($fcnt)".($fcnt ? " : " : " --- --- del : ").filesize(BDIR."$obj_dir/$file");
            if(!$fcnt && $act=='move'){
                rename(BDIR."$obj_dir/$file", BDIR."$obj_bak/$file");
            }
        }
        return $res;
    }

}

/*
define('BDIR', 'E:/www/house');

$odir = '/public/static/site/img-house';
$obak = '/public/static/site/img-hbak';
$ofix = '/img-house';

$froms = [
    '/public/static/site/css',
    '/public/static/site/js',
    '/application/index/view/house',
    '/application/index/view/public',
];

$res = devCoder::stScan($odir, $obak, $ofix, $froms);
dump($res);
*/
