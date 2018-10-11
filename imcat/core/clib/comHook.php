<?php
namespace imcat;

// Hook类
 
class comHook extends comCron{
    
    public $frun = '/store/_rlock_hook.txt';
    private $tpl = ''; 
    private $mkv = ''; 
    
    // [hook_key]($mkv,$tpl), 
    static function listen($act='',$paras=array()){
        if(is_string($paras)){ // [hook_key]($mkv,$tpl)
            new self($act,$paras);
        }elseif(strpos($act,'::')>0){ // call('exuUser::reg',$user)
            $tmp = explode('::',$act);
            $cls = $tmp[0]; $method = $tmp[0];
            $hook = new $cls($paras);
            $re = $hook->$method();
        }else{ // [user_act]('reg',$user)
            $row['kid'] = "hook_$act";
            $re = self::rone($row,$paras);
        }
    }

    //function __destory(){  } mkv+tpl url+file
    function __construct($mkv,$tpl,$upd=1){
        if(empty($mkv)||empty($tpl)) return;
        if(strpos('.',$mkv)){
            $tmp = explode('.',$mkv);
            $tmp[1] = 'detail';
            $mkv = implode('.',$tmp);
        }
        $this->init($mkv,$tpl);
        $this->rlist();
        $upd && $this->update();
    }
    
    // init 
    function init($mkv='',$tpl=''){
        $this->db = glbDBObj::dbObj();
        $this->stamp = $_SERVER["REQUEST_TIME"]; 
        if(!extCache::cfGet($this->frun,$this->rgap)){
            $whr = " exnext<'".$this->stamp."' AND enable=1 AND hkflag=1";
            $whr .= $mkv ? " AND (hkmkv='0' OR hkmkv='$mkv')" : " AND hkmkv='0'";
            $whr .= $tpl ? " AND (hktpl='0' OR hktpl='$tpl')" : " AND hktpl='0'";
            $this->jobs = $this->db->table($this->tab)->where($whr)->select();
        } 
    }
    
    // 运行列表
    function rlist(){
        if(!empty($this->jobs)){
            foreach($this->jobs as $row){
                $rdo = $this->rone($row);
                $next = $this->stamp + extCache::CTime($row['excycle'].$row['excunit']);
                $this->jres[$row['kid']] = array(
                    'rdo' => $rdo, 
                    'next' => $next,
                );
            }
        }
    }

}

/*

*/
