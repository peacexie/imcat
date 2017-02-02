<?php

// usrPerm : 单独分离出来
class usrPerm{    
    
    // 
    static function issup(){
        $user = user('Admin');
        return @$user->uperm['grade']=='supper';
    }    
    
    // fix : '';end;full
    //usrPerm::run(array('pcheck,about','padd,about'));
    //usrPerm::run('pfile','admin/groups.php');
    static function run($key,$val='',$fix='end'){
        $msg = self::check($key,$val); 
        if(empty($fix)){
            return $msg;
        }elseif(in_array($fix,array('end','full'))){ 
            if($msg){
                if($fix=='full'){
                    glbHtml::page();
                    glbHtml::page('body');
                }
                $msg = str_replace('; ',"; <br>\n",$msg);
                $msg = "\n<p class='err'>NO Permission: <br>\n$msg</p>";
                die(glbHtml::page('end',$msg));
            }
        }elseif(in_array($fix,array('js'))){
            return $msg; /// ??? 
        }else{
            return $msg;
        }
    }
    
    // re : str, ''
    static function check($key,$val=''){
        $user = user();
        if($key=='usess'){ 
            $str = comSession::get(self::getSessid()); 
            $re = strstr($str,$val) ? '' : "$key:$val";
            return $re;        
        }elseif(self::issup()){ 
            return ''; //超级管理员
        }elseif(is_array($key)){
            return self::pmArr($key);
        }else{
            return self::pmOne($key,$val);
        }
    }
    
    // utype,login,model,grade,p*,usess,
    // re: str, ''
    static function pmStr($key){
        $user = user();
        $re = '';
        if(in_array($key,array('utype','uflag'))){
            $key = str_replace(array('utype','uflag'),array('usertType','userFlag'),$key);
            return $user->$key;
        }elseif(in_array($key,array('model','grade'))){
            return $user->uperm[$key];
        }elseif(substr($key,0,1)=='p' && isset($user->uperm[$key])){
            return $user->uperm[$key];
        }else{
            return '';
        }
    }
    
    // re : str, '', -
    static function pmOne($key,$val){
        if(empty($key) || empty($val)){
            return '-';
        }else{ 
            $str = self::pmStr($key); 
            if($key=='pfile' && $val=='(auto)') $val = basReq::arr('file'); 
            if(strpos($str,$val)){
                return '';
            }else{
                return "$key:$val";
            }
        }
    }
    
    // re : str, ''
    static function pmArr($arr){
        $re = '';
        foreach($arr as $v){
            $a = explode(',',"$v,");
            $rt = self::pmOne($a[0],$a[1]);
            if(!empty($rt)){
                $re .= (empty($re) ? '' : '; ')."$a[0]:$a[1]";
            }
        }
        return $re; 
    }
    
    // 从uperm['cfgs']中取得权限key(s)
    static function pmCfgs($cfgs='',$re='_arr'){
        $cfgs = is_object($cfgs) ? $cfgs->uperm['cfgs'] : (is_array($cfgs) ? $cfgs['cfgs'] : $cfgs);
        $a = basElm::text2arr($cfgs);
        if($re=='_arr'){ 
            return $a; 
        }else{
            return isset($a[$re]) ? $a[$re] : '';
        }    
    }
    
    // 取得Upload权限key(s)
    static function pmUpload($uobj=''){
        if(empty($uobj)){
            $uobj = user();
        }
        if(@$uobj->uperm['grade']=='supper'){ 
            return array('upsize1'=>'(supper)','uptypes'=>'(supper)',);
        }
        $rcfg = self::pmCfgs($uobj->uperm['cfgs']);
        $rcfg['uptypes'] = array();
        $upex = empty($uobj->uperm['pextra']) ? array() : explode(',',$uobj->uperm['pextra']);
        if(!empty($upex)){
            $tcfg = read('filetype','sy');
            foreach($upex as $k){
                if(isset($tcfg[$k])){
                    $rcfg['uptypes'] = array_merge($rcfg['uptypes'],$tcfg[$k]);
                }
            }
        }
        if($rcfg['uptypes']){
            $rcfg['uptypes'] = explode(',','.'.implode(',.',$rcfg['uptypes']));    
        }
        if(empty($rcfg['upsize1'])) $rcfg['upsize1'] = 10; //KB
        if(empty($rcfg['uptypes'])) $rcfg['uptypes'] = array('.gif', '.jpg'); //KB
        return $rcfg;
    }
    
    // type : array('sip'=>$sipck,'sua'=>$ua,'sid'=>$enc)
    static function getUniqueid($mode='Cook',$type='sip'){ //Unique
        $re = comSession::guid('safil','Uniqueid',$mode);
        return $re[$type];
    }
    
    // re : str,
    static function getSessid(){
        $tid = preg_replace("/[^\w]/", '', cfg('safe.safil'));
        return 'pmSessid_'.$tid;
    }
    
}
