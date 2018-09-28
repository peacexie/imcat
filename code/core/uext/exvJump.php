<?php
namespace imcat;

// 显示相关函数; 单独函数可先用new exvJump();自动加载
class exvJump{

    static $jcfg = array();

    // redir-站内跳转相关 ======================================== 

    //function __destory(){  }
    function __construct($run=1){
        $run && $this->run();
    }

    function run(){
        $qstr = $_SERVER['QUERY_STRING'];
        if(strpos($qstr,':')>0){
            $arr = explode(':',$qstr);
            $mth = 'do'.ucfirst($arr[0]);
            $this->$mth($arr[1]); // lang:/advs:
        }elseif(strpos($qstr,'.')){
            $arr = explode('.',$qstr);
            $mth = 'do'.($arr[0]=='dir'?'Dirs':'Mods');
            $this->$mth($arr[0],$arr[1]); // dir./mod.
        }else{ // yl
            $ret = self::tuGet($qstr,1);
            if(empty($ret)) header("Location:".PATH_PROJ);
        }
    }

    // /ajax/redir.php?skin:blue:&recbk=redir
    function doSkin($skin){
        $recbk = req('recbk',@$_SERVER["HTTP_REFERER"]);
        $file = DIR_VENDUI."/bootstrap/css/bootstrap.$skin.css";
        file_exists($file) && comCookie::oset('skin',$skin,30*86400);
        if($recbk && !strpos($recbk,'plus/ajax/redir.php')){
            header("Location: $recbk");
        }else{
            die("::$file:$recbk::");
        }
    }

    // /ajax/redir.php?lang:cn:&recbk=redir
    function doLang($lang){
        $recbk = req('recbk',@$_SERVER["HTTP_REFERER"]);
        $file = DIR_CODE."/lang/kvphp/core-$lang.php";
        file_exists($file) && comCookie::oset('lang',$lang,30*86400); 
        if($recbk && !strpos($recbk,'plus/ajax/redir.php')){
            header("Location: $recbk");
        }else{
            die("::$file:$recbk::");
        }
    }
    // /ajax/redir.php?advs:encode
    function doAdvs($encode){
        $mkv = explode(',',comConvert::sysBase64($encode,'de'));
        $mod = $mkv[0];
        $aid = @$mkv[1];
        $url = @$mkv[2]; 
        if(empty($mod) || empty($aid) || empty($url)){
            exit("Error: [$mod,$aid,$url]");
        }else{
            $db = db();
            $db->query("UPDATE ".$db->table("advs_$mod",2)." SET click=click+1 WHERE aid='$aid'");
            header("Location: $url");    
        }
    }
    // /ajax/redir.php?news.2015-a1-fhh1
    // /index.php?indoc.1234-56-7890
    function doMods($mod,$kid){
        $mods = array('indoc');
        if(in_array($mod,$mods)){
            if(basEnv::isMobile()){ // basEnv::isWeixin() || 
                $tpl = 'mob';
            }else{
                $tpl = 'umc';    
            }
        }else{
            $sdirs = vopTpls::etr1('show'); 
            $tpl = $_cbase['tpl']['tpl_dir'] = $sdirs['_defront_'];
            $hid = $sdirs['_hidden_'];
            unset($sdirs['_defront_'],$sdirs['_deadmin_'],$sdirs['_hidden_']);
            foreach($sdirs as $k=>$v){ 
                if(in_array($mod,$v)){
                    $tpl = $k;
                    break;
                }
            } 
        } 
        $url = surl("$tpl:$mod.$kid");
        if(strpos($url,'close#')) basMsg::show("$mod,$kid,$tpl<br>$url",'die');
        header("Location: $url"); 
    }
    // /index.php?dir.yscode
    function doDirs($mod,$kid){
        $redir = self::getCfgs('redir');
        if(isset($redir[$kid])){
            header("Location: ".$redir[$kid]);
        }
    }

    // sites-分站跳转相关 ======================================== 

    // 获得多语言-跳转地址
    static function getLang(){
        $langs = self::getCfgs('langs');
        $_def = self::getCfgs('_defs');
        $nkey = $_def['lang']; //未找到地区时的默认网站
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
        $lang = 'en'; // zh,cn,en
        foreach($langs as $key=>$kname){
            if($lang==$key){
                $nkey = $kname;
                break;        
            }
        }
        $nurl = vopUrl::fout("$kname:0"); 
        return $nurl;
    }
    // 获取ip对应地址
    static function getAddr($userip){
        $_def = self::getCfgs('_defs');
        $api = $_def['api']; 
        $ipObj = new extIPAddr($api);
        $addr = $ipObj->addr($userip);
        #echo "$api,$userip,$addr";
        return $addr;
    }
    // 获得分站-跳转地址
    static function getDurl($uaddr){
        $jcfg = self::getCfgs();
        $nkey = $jcfg['_defs']['site']; //未找到地区时的默认网站
        foreach($jcfg['sites'] as $key=>$kname){
            if(strstr($uaddr,$kname)){
                $nkey = $key;
                break;        
            }
        }
        $nurl = "http://$nkey.{$jcfg['_defs']['domain']}/"; // 组装完整url
        return $nurl;
    }

    // tab
    static function tab($qstr){
        $data = exvJump::getCfgs('sites');
        $_def = exvJump::getCfgs('_defs');
        if($qstr=='html'){
            exvFunc::navShow($data,'{key}',"http://{key}.{$_def['domain']}/");
        }else{
            glbHtml::head('html');
            echo comParse::jsonEncode($data);
        }
    }
    // go
    static function go($qstr){
        // 获取ip,可在地址栏输入?ip用于调试
        $userip = ($qstr && strpos($qstr,'.')) ? $qstr : comSession::getUIP(); 
        if($qstr && strpos($qstr,':debug')){
            $qstr = 'debug';
            $userip = str_replace(':debug','',$userip);
        }
        // 获取:ip对应地址/跳转url
        $addr = exvJump::getAddr($userip);
        $durl = exvJump::getDurl($addr);
        if($qstr=='debug'){
            $data = array('userip'=>$userip,'addr'=>$addr,'dir_url'=>$durl,);
            exvFunc::navShow($data,0);
        }else{
            header("Location:$durl");  
        }
    }

    // pub-公共方法 ======================================== 

    // 获得ujump配置
    static function getCfgs($key=''){
        if(empty(self::$jcfg)){
            self::$jcfg = glbConfig::read('vjump','ex');
        }
        return $key && isset(self::$jcfg[$key]) ? self::$jcfg[$key] : self::$jcfg;
    }

    // tiny-url-短链接相关 ======================================== 

    // 设置一个短url地址
    // 利用保留字符做前缀:iloz:用于特殊场合
    static function tuSet($url,$pre='',$n=0){
        $db = glbDBObj::dbObj();
        if(empty($n)){
            // http://{host}/1234567 : 15
            if(strlen($url)<strlen($_SERVER["HTTP_HOST"])+16) return $url;
            $row = $db->table('token_turl')->where("url='$url'")->find();
            if($row) return $row['kid'];
        }
        $m = $n<3 ? 2 : ($n>5 ? 5 : $n);
        $kid = ($pre?$pre:basKeyid::kidRand('22',1)).basKeyid::kidRand('30',$m);
        $rec = $db->table('token_turl')->where("kid='$kid'")->find(); 
        if($rec){
            return self::tuSet($url,$pre,++$n);
        }else{
            $db->table('token_turl')->data(array('kid'=>$kid,'url'=>$url))->insert(0);
            return $kid;
        }
    }
    // 查询一个短url地址
    static function tuGet($kid,$dir=0){
        $db = glbDBObj::dbObj();
        $row = $db->table('token_turl')->where("kid='$kid'")->find();
        if(!$row) return false;
        $url = $row['url'];
        if($dir){ 
            header("Location:$url");
            die();
        }else{
            return $url;
        }
    }

}
