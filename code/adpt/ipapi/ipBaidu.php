<?php
// 获取ip地址-Sina
class ipBaidu{
    
    public $url = 'http://api.map.baidu.com/location/ip?ak=3GGtGlCtbAGa1GYK70XFX2Rb&coor=bd09ll&ip='; 
    public $cset = 'utf-8';
    
    // 获取数据
    //function getAddr($ip){}
    
    // 过滤处理
    function fill($addr){
        $addr = json_decode($addr,1); 
        if(!empty($addr['address'])){
            $addr = $addr['address'];
        }elseif(!empty($addr['message'])){
            $addr = $addr['message'].'('.$addr['status'].')'; 
        }else{
            $addr = var_export($addr,1);
        }
        return $addr;
    }
}

