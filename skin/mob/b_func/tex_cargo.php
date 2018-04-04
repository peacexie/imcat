<?php
/*
单个模板扩展函数
*/ 
class tex_cargo{
    
    #protected $prop1 = array();
    
    static function expwhr($flag=0){ 
        $re = '';
        // exp_xxx
        $flist = basLang::ucfg('fsystem'); 
        foreach($flist as $k=>$v){
            if(strstr($k,'exp_')){
                $val = req($k);
                if(!empty($val)){
                    if(strstr($k,'exp_s')){
                        $re .= (empty($re) ? '' : ' AND ')."$k='$val'";
                    }elseif(strstr($k,'exp_m')){
                        $re .= (empty($re) ? '' : ' AND ')."$k LIKE '$val'";
                    }
                }
        }   } 
        // price: price=b10, 300~500, u1000
        $area = req('price');
        if(strstr($area,'~')){ //$area && 
            $arr = explode('~',$area);
            $arr[0] && $re .= (empty($re) ? '' : ' AND ')."price>='$arr[0]'";
            $arr[1] && $re .= (empty($re) ? '' : ' AND ')."price<='$arr[1]'";
        } 
        return $re;
    }

}
