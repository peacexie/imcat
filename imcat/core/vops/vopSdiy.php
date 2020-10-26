<?php
namespace imcat;

/* Sdiy/Smart-Diy: (自定义)简易模式
* cfg设置
  - home: 首页模板, 默认:home
  - _mkv: 首页mkv, 默认:home
  - _tpl: 默认其他模板, 默认:page
  - {mkv}: 指定其他mkv模板, 自定义
*/

class vopSdiy extends vopShow{

    //function __destory(){  }
    function __construct($dir, $cfg=[], $cex=[]){
        $def = ['home'=>'home', '_mkv'=>'home', '_tpl'=>'page'];
        $cfg = empty($cfg) ? $def : array_merge($def, $cfg);
        $this->imkv($cfg);
        $this->view($dir, $cfg, $cex);
    }

    function view($dir, $cfg='', $cex=[]){
        global $_cbase;
        $_cbase['tpl']['vbase'] = dirname($dir);
        $_cbase['tpl']['vdir'] = basename($dir); 
        $_cbase['run']['tplcfg'] = [ // 支持surl(标签函数)
            empty($cex[0]) ? '' : $cex[0], 
            $this->burl($cex), 
            empty($cex[2]) ? '?' : $cex[2]
        ];
        $this->tplCfg = $_cbase['tpl']; 
        $mkv = $_cbase['mkv']; //dump($mkv);
        if(isset($cfg[$mkv['mkv']])){
            $tpl = "tpls/".$cfg[$mkv['mkv']];
        }elseif(in_array($mkv['mkv'],$cfg)){
            $tpl = "tpls/".$mkv['mkv'];
        }else{
            $tpl = "tpls/".$cfg['_tpl']; 
        } //dump($tpl);
        $tpl = str_replace(['{mod}','{key}'] ,[$mkv['mod'],$mkv['key']], $tpl);
        $_cbase['run']['tplname'] = $tpl; //echo $tpl;
        $tplfull = vopComp::main($tpl);
        include $tplfull;
    }

    function burl(&$cex){
        if(empty($cex[1])){
            $cex[1] = $_SERVER['SCRIPT_NAME'];
            if(!empty(PATH_PROJ)){
                $cex[1] = substr($cex[1], strlen(PATH_PROJ));
            }
        }
        return $cex[1];
    }

    // mkv初始化
    function imkv($cfg){
        global $_cbase;
        $q = vopUrl::route($cfg['_mkv']); 
        $mkv = strpos($q,'&')>0 ? substr($q,0,strpos($q,'&')) : $q; 
        if(strpos($mkv,'-')){
            $tmp = explode('-', $mkv);
            if(count($tmp)>2 || empty($tmp[1])){ 
                vopShow::msg("b:[$mkv]:".basLang::show('vop_parerr')); 
            }
            $re = ['mod'=>$tmp[0], 'key'=>$tmp[1]];
        }else{
            $re = ['mod'=>$mkv, 'key'=>''];
        }
        $re['q'] = $q;
        $re['mkv'] = $mkv;
        $_cbase['mkv'] = $re;
    }
    
    // 解析一个md-key的数据
    static function imds($key, $ext='md'){
        global $_cbase;
        $mkv = $_cbase['mkv'];
        $key = str_replace(['{mod}','{key}'] ,[$mkv['mod'],$mkv['key']], $key);
        $pfile = vopTpls::tinc("$key.$ext", 0);
        if(!file_exists($pfile)){ //echo $pfile;
            glbHtml::httpStatus('404');
            return "c:[$key]:".basLang::show('vop_parerr');
        } 
        $mds = vopComp::incBlock("{md:\"$key\"}");
        return $mds;
    }

    // 解析一个md-key的列表
    static function mdtab($mdk='mds/home', $hk='###', $lik='*'){
        $fp = vopTpls::tinc("$mdk.md", 0);
        $data = comFiles::get($fp, 1);
        $darr = explode("\n", $data); 
        $res = []; $pid = ''; $title = '';
        foreach ($darr as $row) {
            $row = trim($row); 
            if(strpos($row,"$hk ")===0){
                $arr = self::mdrow($row, $hk);
                $pid = $arr[1];
                $res[$arr[1]] = ['title'=>$arr[0]];
            }
            if(strpos($row,"$lik ")===0){
                $arr = self::mdrow($row, $lik);
                $res[$pid]['sub'][$arr[1]] = $arr[0];
            }
        } //dump($res);
        return $res;
    }
    static function mdrow($row, $key){
        $tmp = trim(str_replace("$key ",'',$row)); //echo ".$tmp.";
        $arr = strpos($tmp,' /') ? explode(' /',$tmp) : explode(' ',$tmp);
        return $arr;
    }

    // re: now, prev, next
    static function mdtitle($res, $re='now'){
        global $_cbase;
        $key = $_cbase['mkv']['mkv'];
        $pid = $ptitle = '';
        foreach ($res as $pk=>$part) {
            foreach ($part['sub'] as $sk=>$title) {
                $nkey = "$pk-$sk";
                if($re=='next' && $pid==$key){
                    return [$nkey, $title];
                }elseif($nkey==$key){
                    if($re=='now'){
                        return [$nkey, $title];
                    }elseif($re=='prev'){
                        return [$pid, $ptitle];
                    }
                }
                $pid = $nkey; $ptitle = $title;
            }
        }
        return ['', ''];
    }

    // views下,一个dir的文件列表
    static function vdtab($dir='comm', $root=''){
        $root || $root = DIR_VIEWS;
        $tmp1 = comFiles::listDir("$root/$dir");
        $res = []; 
        foreach ($tmp1['dir'] as $sdir=>$time) {
            $tmp2 = comFiles::listDir("$root/$dir/$sdir");
            if(!empty($tmp2['file'])){
                $res[$sdir] = [
                    'cnt' => count($tmp2['file']),
                    'sub' => $tmp2['file'],
                ];
            }
        } //dump($res);
        return $res;
    }

}
