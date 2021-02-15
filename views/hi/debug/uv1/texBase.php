<?php
namespace imcat\umc;

use imcat\basEnv;
use imcat\basJscss;
use imcat\comParse;
use imcat\comTypes;
use imcat\usrBase;

/*
公共模板扩展函数
*/ 
class texBase{
    
    //protected $xxx = array();
    static function tips_init($obj){ 
        $run = cfg('run');
        $_micfg = read('mkvu.i'); 
        $perm = $permOrg = req('perm');
        $perm = $perm=='.login' ? lang('user.tex_base_ulogin') : lang('user.tex_base_uperm').'»'.comTypes::getLnks(comTypes::getLays($_micfg,$perm),'([k])[v]');
        $from = req('from');
        $from = substr($from,0,2)=='q-' ? comParse::urlBase64(substr($from,2),1,1) : $from;
        $ulogin = 'uio-login';
        $uapply = 'uio-apply';
        $runinfo = '';
        $runinfo .= "".$run['query']."(queries)/".round(memory_get_usage()/1024/1024, 3)."(MB); ";
        $runinfo .= "tpl:".(empty($run['tplname']) ? '(null)' : $run['tplname'])."; "; //tpl 
        $tipmsg =  lang('user.tex_base_vlimit');
        $re = array();
        foreach(array('perm','permOrg','from','ulogin','uapply','runinfo','tipmsg') as $k){
            $re[$k] = $$k;
        }
        return $re;
    }
    
    
    static function init($obj){ 
        global $_cbase;
        $user = usrBase::userObj('Member'); 
        $_micfg = read('mkvu.i'); 
        $pkey = "$obj->mod-"; //obj: type:detail,mext,mtype,mhome
        if($obj->type=='detail'){
            $pkey .= 'd';
        }elseif($obj->type=='mhome'){
            $pkey .= 'm';
        }else{
            $pkey .= $obj->key;    
        } 
        $pnow = empty($_micfg[$pkey]['cfgs']) ? '.login' : $_micfg[$pkey]['cfgs']; //1, (empty), .guest
        if($pnow==1) $pnow = $pkey;
        $_cbase['tpl']['tplpext'] = "";
        if($pnow=='.guest' || in_array($obj->mod,$obj->ucfg['hcfg']['pskip'])){ 
            return; // 游客可操作 or 提示页本身 or 免认证
        }
        if($pnow=='.login'){ // def:.login 
            if($user->userFlag=='Login') return; // 需要登录 and 已登录
        }else{ // 1:set
            $pstr = $user->uperm['mkvu'];
            if(strpos(":,$pstr,",",$pnow,")) return;
        }
        $from = $obj->ucfg['q']==$obj->mkv ? $obj->mkv : 'q-'.comParse::urlBase64($obj->ucfg['q'],0,1); 
        header('Location:'."?uio-tips&from=$from&perm=$pnow"); 
    }
    
    static function pend(){
        $tpl = cfg('tpl');
        $base = $tpl['tplpend'];
        $ext = $tpl['tplpext'];
        $base || $base = basEnv::isMobile() ? '' : 'menu'; //jstag,menu,
        $js = "setTimeout(\"jcronRun()\",3700);\n";
        $ext && $js .= "$ext;\n";
        strstr($base,'jstag') && $js .= "jtagSend();\n";
        strstr($base,'menu') && $js .= "jsactMenu();\n";
        echo basJscss::jscode("\n$js")."\n";
    }

}
