<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
// 消息回复（被动回复）

class wexReplyAdmin extends wysReply{
    
    public $_db = NULL;
    
    function __construct($post,$cfg,$re=0){ 
        parent::__construct($post,$cfg); 
    }
    
    // 文本消息
    function reText(){  
        $detail = $this->post->Content; 
        if(strlen($detail)==2){ // && basStr::isKey($detail,2)
            $detail = "cn$detail";
            $arr = read('china.i'); 
            if(isset($arr["$detail"])){
                $str = '';
                foreach($arr as $k=>$v){
                    if($k==$detail){
                        $str .= "[$k]".$v['title']." (".$v['cfgs'].") 详情如下：";    
                    } //
                    $link1 = $link2 = '';
                    if($v['pid']==$detail){
                        $map = explode('map=',str_replace(array("\r","\n"),array('',''),$v['cfgs']));
                        if(!empty($map[1]) && count(explode(',',$map[1]))==2){
                            $mpoint = explode(',',$map[1]);
                        }else{
                            $mpoint = array();
                        } 
                        $link1 = "<a href='http://m.baidu.com/s?word={$v['title']}'>[$k]{$v['title']}</a>";
                        //$link2 = empty($mpoint) ? "" : "<a href='".(wysBasic::fmtUrl('{root}/run/umc.php'))."mkv=uio-wxlocal&map={$mpoint[1]},{$mpoint[0]}'>地图</a>";
                        $link2 = empty($mpoint) ? "" : "\n坐标：$map[1]";
                        $str .= "\n{$link1} {$link2}"; 
                    }
                } 
                die($this->remText($str));
            }
        }
        $this->reTextBase();
    }
    
}
