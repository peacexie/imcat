<?php
/*
公共模板扩展函数
*/ 
class tex_base{
    
    //protected $xxx = array();
    static function tips_init($obj){ 
        $run = cfg('run');
        $_mumem = read('mumem');
        $perm = $permOrg = req('perm');
        $perm = $perm=='.login' ? lang('user.tex_base_ulogin') : lang('user.tex_base_uperm').'»'.comTypes::getLnks(comTypes::getLays($_mumem['i'],$perm),'([k])[v]');
        $from = req('from');
        $from = substr($from,0,2)=='q-' ? comParse::urlBase64(substr($from,2),1,1) : $from;
        $ulogin = 'home-login';
        $uapply = 'home-apply';
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
        $user = user('Member'); 
        if($obj->mkv=='home') header('Location:'."?user");
        $_micfg = read('mumem.i'); 
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
        $pmarr = empty($user->uperm['pmusr']) ? array() : explode(',',$user->uperm['pmusr']);
        if($pnow=='.guest' || in_array($obj->tplname,array('user/tips','user/home')) || in_array($obj->mod,$obj->ucfg['u']['umc_frees'])){ 
            //游客可操作 or 提示页本身 or 跳转首页
        }elseif($pnow=='.login'){ 
            //需要登录 and 已登录
        }elseif(in_array($pnow,$pmarr)){ //登录用户有权限
            //登录用户有权限
        }else{
            $from = $obj->ucfg['q']==$obj->mkv ? $obj->mkv : 'q-'.comParse::urlBase64($obj->ucfg['q'],0,1); 
            header('Location:'."?mkv=user-tips&from=$from&perm=$pnow"); #echo "$from"; #
        } 
        $_cbase['tpl']['tplpext'] = "var pmusr='".implode(',',$pmarr)."';";
        return $user;
    }
    
    static function pend(){
        $tpl = cfg('tpl');
        $base = $tpl['tplpend'];
        $ext = $tpl['tplpext'];
        $base || $base = 'menu'; //jstag,menu,
        $js = "setTimeout(\"jcronRun()\",3700);\n";
        $ext && $js .= "$ext;\n";
        strstr($base,'jstag') && $js .= "jtagSend();\n";
        strstr($base,'menu') && $js .= "jsactMenu();\n";
        echo basJscss::jscode("\n$js")."\n";
    }

}
