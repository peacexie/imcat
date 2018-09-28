<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
// 本系统菜单操作
// 如果本系统修改,就改这个文件，不用改wmp*文件

class wysMenu extends wmpMenu{
//class wysBasic{
    
    public $oauth = NULL;
    
    function __construct($cfg=array()){
        if(strpos($cfg['kid'],'08_')) die('Forbid');
        parent::__construct($cfg); //$this->wxmenu = new cls_wecMenu($cfg);
        $this->oauth = new wmpOauth($cfg);
    }
    
    // 08格式的menu数组，
    function get(){ 
        $menu = $this->menuGet(); 
        $re=array(); $i=0; $j=0; 
        if(isset($menu['menu']['button'])){
            $menu = $menu['menu']['button']; 
            foreach($menu as $k=>$v){
                $i++; $j=0;
                if(empty($v['sub_button'])){
                    $val = isset($v['url']) ? $v['url'] : $v['key'];
                    $re["{$i}0"] = array('type'=>$v['type'], 'name'=>$v['name'], 'val'=>$val, );
                }else{
                    $re["{$i}0"] = array('name'=>$v['name']);
                    foreach($v['sub_button'] as $k2=>$v2){
                        $j++; 
                        $val = isset($v2['url']) ? $v2['url'] : $v2['key'];
                        $re["$i$j"] = array('type'=>$v2['type'], 'name'=>$v2['name'], 'val'=>$val, );
                    }
                }
            }
        }
        return $re;
    }

    // {$mobileurl},{cms_abs},oauth=base/uinfo,
    function create($mcfg=array()){ 
        $menu = array(); $re = array(); 
        for($i=1;$i<=3;$i++){
            if(empty($mcfg["{$i}0"]['name'])) continue;
            $rei = array();
            $rei['name'] = $mcfg["{$i}0"]['name'];
            $subs = array();
            for($j=1;$j<=5;$j++){
                if(empty($mcfg["{$i}{$j}"]['name'])) continue;
                $name = $mcfg["{$i}{$j}"]['name'];
                $val = $this->fmtUrl($mcfg["{$i}{$j}"]['val']);
                $type = strpos($val,'://') ? 'view' : 'click'; //其它操作?! 
                $key = $type=='view' ? 'url' : 'key';
                $subs[] = array('type'=>$type, 'name'=>$name, $key=>$val, );
            }
            if(empty($subs)){
                $val = $this->fmtUrl($mcfg["{$i}0"]['val']);
                $type = $rei['type'] = strpos($val,'://') ? 'view' : 'click'; //其它操作?! 
                $key = $type=='view' ? 'url' : 'key';
                $rei[$key] = $val;
            }else{
                $rei['sub_button'] = $subs;
            }
            $re[] = $rei; 
        } 
        return $this->menuCreate($re);
    }

    // delete
    function del(){ 
        return $this->menuDelete();
    }
    
    //此方法，扩展需求比较多
    function fmtUrl($url, $enc=0){ 
        if(basStr::isKey($url,2,255)){
            //; Mylocal, Haibao
        }elseif(substr($url,0,13)=='oauth:snsapi_'){
            $a = explode(':',$url); // oauth:scope:state:{root}/mob.php
            $dir = substr($url,strlen($a[0])+strlen($a[1])+strlen($a[2])+3);
            $dir .= "&kid"."=".$this->cfg['kid'];
            $dir = self::fmtUrl($dir, 1);
            $url = $this->oauth->getCode($dir, $a[1], $a[2]); 
        }else{ // strpos($url,'://'), substr($url,0,7)=='{root}/'
            return wysBasic::fmtUrl($url, $enc);
            // http://www.d.com, {root}/mob.php
        }
        return $url;
    }
    
    static function getMenuData($appid){ 
        $re = array(); 
        $data = db()->table('wex_menu')->where("appid='$appid'")->select(); 
        foreach($data as $row){
            $re[$row['key']] = $row;
        } 
        return $re;
    }

}
