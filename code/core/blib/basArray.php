<?php

// Array类
class basArray{    
    
    // 按数组值长度获取部分项
    static function lenParts($ara,$arb,$nlen,$mlen=15) {
        if(empty($ara)) return array();
        $rea = $reb = array();
        foreach ($ara as $k=>$val) {
            $len = mb_strlen($val,'UTF8');
            if($len>=$mlen){
                $len = $mlen;
            }
            if($nlen==$len){
                $rea[] = $val;
                $reb[] = $arb[$k];
            } 
        } //dump($rea); dump($reb);
        return array($rea,$reb);
    }

    // 按数组值长度排序
    static function lenOrder($arr,$mlen=15,$re='lenkey') {
        if(empty($arr)) return array();
        $arb = $arc = array(); // 
        foreach($arr as $val){
            $len = mb_strlen($val,'UTF8');
            if($len>=$mlen) $len = $mlen;
            $arb[$len][] = $val;
        }
        if($re=='lenkey'){
            return $arb;
        }
        for($i=$mlen; $i>=1; $i--) { 
            if(empty($arb[$i])) continue;
            $arc = array_merge($arc,$arb[$i]);
        }
        return $arc;
    }

    // 从Object转化为array
    static function fromObject($obj,$skip='') {
        $ref = new ReflectionClass($obj); 
        $props = $ref->getProperties(); 
        $arr = array();
        if($skip=='def'){ 
            $skips = array('top','auser','atime','aip','euser','etime','eip'); 
        }else{
            $skips = array();
        }
        foreach ($props as $prop) {
            $prop->setAccessible(true);
            $key = $prop->getName();
            if(in_array($key,$skips)){
                continue;
            }
            $arr[$key] = @$prop->getValue($obj);
            $prop->setAccessible(false);
        }
        return $arr;
    }
    // 从文件获取array
    // cfgs: start, end, len
    // skips: array('//','---','###') 
    static function fromFile($file,$cfgs=array()){
        if(!file_exists($file)) return array();
        $arr = file($file); $re = array(); 
        $cfgs['len'] = isset($cfgs['len']) ? $cfgs['len'] : 6;
        foreach($arr as $row){
            $row = str_replace(array("\r","\n"),'',$row);
            if(!empty($cfgs['start']) && strpos($row,$cfgs['start'])){
                $re = array();
                continue;
            }
            if(!empty($cfgs['end']) && strpos($row,$cfgs['end'])){
                break;
            }
            if(!empty($cfgs['len']) && strlen($row)<$cfgs['len']){
                continue;
            }
            if(!empty($cfgs['skips']) && self::inStr($cfgs['skips'],$row)){
                continue;
            }
            $re[] = $row;    
        }
        return $re;
    }
    
    // 从字段获得option的array
    static function opaFields($items,$types=''){  
        $a = array();
        foreach($items as $k=>$v){ 
            if(empty($types) || strstr($types,$v['dbtype'])){
                $a[$k] = $v['title'];
            }
        }
        return $a;
    }
    // 从类别获得option的array
    static function opaItems($items,$types='',$frame=1){  
        $a = array();
        foreach($items as $k=>$v){ 
            $deep = intval($v['deep']);
            $k2 = ($frame && !empty($v['frame'])) ? "^group^$k" : $k;
            $a[$k2] = ($deep>'1' ? str_repeat('&#12288;',$deep-1) : '').$v['title'];
        }
        return $a;
    }
    
    // 比较数组:
    // basArray::cmpArr($new,$old,'Item'); //code,item
    static function cmpArr($new,$old,$type='code'){
        $method = "cmp$type"; 
        $res = array('new'=>'','old'=>''); 
        foreach(array('new','old') as $key){ 
            foreach($$key as $ki=>$val){ 
                $one = self::$method(($key=='new' ? $old : $new),$val,$ki);
                $res[$key] .= "<li class='$one[1]'>$one[0]</li>\n";
            }
        }
        similar_text(var_export($new,1),var_export($old,1),$per);
        $res['per'] = $per;
        return $res;
    }
    // 在一个数组中，比较一行配置代码；
    static function cmpCode($arr,$val){
        $valbak = $val;
        $val = trim(str_replace(array('<','>',"\n","\r"),array('&lt;','&gt;','',''),$val));
        $fphp = self::inArr($arr,'<?');
        $fnote = substr($val,0,2)=='//' || substr($val,0,1)=='#';
        if(strlen($val)<3 || ($fphp && $fnote)){
            $cls = 'gray'; $val .= "";
        }elseif(!in_array($valbak,$arr)){
            $cls = 'cF00';
            if(strpos($val,",") && strstr($val,'define(')){
                $tmp = explode(",",$val);
                if(!empty($tmp[0]) && self::inArr($arr,trim($tmp[0]))){ 
                    $val = $tmp[0].",<span class='c00F'>".substr($val,strlen($tmp[0])+1)."</span>"; 
                    $cls = ''; 
                }
            }elseif(strpos($val,'=')){
                $tmp = explode('=',$val);
                if(!empty($tmp[0]) && self::inArr($arr,trim($tmp[0]))){
                    $val = $tmp[0]."=<span class='c00F'>".substr($val,strlen($tmp[0])+1)."</span>"; 
                    $cls = ''; 
                }
            }
        }else{
            $cls = '';    
        }
        return array($val,$cls);
    }
    // 在一个数组中，比较另一数组的一项；
    static function cmpItem($arr,$val,$ki){
        $cls = ''; $valbak = $val; //备份用于比较, 以下转码用于显示
        $val = trim(str_replace(array('<','>',"\n","\r"),array('&lt;','&gt;','',''),$val));
        if(is_numeric($ki)){ //数字键
            $cls = in_array($valbak,$arr) ? '' : 'cF00'; 
        }elseif(!isset($arr[$ki])){ //不存在
            $cls = 'cF00'; 
        }elseif($arr[$ki]!=$valbak){ //不相等
            $val = "<span class='c00F'>$val</span>"; 
        }
        $val = "[$ki] => $val";
        return array($val,$cls);
    }
    
    // 检测str中,是否包含$arr中的某项
    // $arr="aaa|bbb"; 可以是|分割的字符串
    // basArray::inStr(array('org','com','net'),'xys@163.com');
    static function inStr($arr,$str){  
        if(empty($str)) return false;
        if(is_string($arr)) $arr = explode('|',$arr); 
        foreach($arr as $v){ 
            if(strstr($str,$v)){
                return true;
            }
        }
        return false;
    }
    
    // 检测$arr中，是否某项包含了str字符串
    // $arr="aaa|bbb"; 可以是|分割的字符串
    // basArray::inArr(array('163.org','163.com','163.net'),'.cn');
    static function inArr($arr,$str){  
        if(empty($str)) return false;
        if(is_string($arr)) $arr = explode('|',$arr); 
        foreach($arr as $v){ 
            if(strstr($v,$str)){
                return true;
            }
        }
        return false;
    }

    /** 
     * 多维数组的合并(相同的字符串键名，后面的覆盖前面的) 
     * @param array $array1 
     * @param array $array2 (先判断array2是不为空的数组)
     */  
    static function Merge($array1,$array2){ 
        if(is_array($array2) && !empty($array2)){//不是空数组的话  
            foreach($array2 as $k=>$v){  
                if(is_array($v) && !empty($v)){  
                    if(isset($array1[$k])) $array1[$k] = self::Merge($array1[$k], $v);  
                    else $array1[$k] = $v;  
                }else{  
                    if(!empty($v)){
                        $array1[$k] = $v;  
                    }  
                }  
            }  
        }else{  
            $array1 = $array2;  
        }  
        return $array1;  
    } 
    
    // array prev元素key(不考虑0键值)
    static function prevKey($a,$key){
        $pk = '';
        foreach($a as $k=>$v){
            if($k==$key){
                return $pk;
            }
            $pk = $k; // 记录上一个key
        }
        return '';
    }
    // array next元素key(不考虑0键值)
    static function nextKey($a,$key){
        $flag = 0; $no = 0;
        foreach($a as $k=>$v){
            $no++;
            if($flag){
                return $k;
            }
            if($k==$key){
                $flag = 1;
            }
        }
        return '';
    }
    
    /**
     * 按数组中指定键的值，对数组进行排序
     * 
     * @param array  $array     要排序的数组
     * @param string $orderkey  指定排序的键，以其值排序
     * @param bool   $keepkey     数字键名是否需要保持不变
     */ 
    static function msort(array &$array, $orderkey='top', $keepkey=false){
        if(!is_array($array) || empty($array) || !function_exists('array_multisort')) return;
        foreach($array as $k => $v){
            $vorder[$k] = $array[$k][$orderkey] = empty($v[$orderkey]) ? 0 : $v[$orderkey];
            $eorder[$k] = $k;
            if($keepkey) $array[$k]['_key'] = $k;
        }
        array_multisort($vorder,SORT_ASC,$eorder,SORT_ASC,$array);
        if($keepkey){
            $na = array();
            foreach($array as $k => $v){
                $key = $v['_key'];
                unset($v['_key']);
                $na[$key] = $v;
            }
            $array = $na;
        }
    }
    
    /**
     * 根据$Key读取数组中的值            
     * @param  array          $array                 源数组
     * @param  string          $Key                 键名，支持'xx.kk.dd'得到$array['xx']['kk']['dd']
     */
    static function get($array=array(), $Key=''){
        if(!is_array($array)) return NULL;
        if(!($KeyArray = self::ParseKey($Key))){
            return NULL;
        }
        foreach($KeyArray as $k){
            $array = isset($array[$k]) ? $array[$k] : NULL;
        }
        return $array;
    }
    
    /**
     * 根据$Key设置数组中的值
     * @param  string          $Key                 键名，支持'xx.kk.dd'为$array['xx']['kk']['dd']赋值
     * @param  string          $value 值            需要设置的值
     * @param  array          $array                 源数组
    */
    static function set(&$array, $Key='',$Value=0){
        if(!is_array($array)) return;
        if(!($KeyArray = self::ParseKey($Key))) return;
        $level = count($KeyArray) - 1;
        foreach($KeyArray as $k => $v){
            if($k < $level){
                $array[$v] = isset($array[$v]) && is_array($array[$v]) ? $array[$v] : array();
                $array = &$array[$v];
            }else{
                $array[$v] = $Value;
            }
        }
    }
    
    /**
     * unsetVal
     */
    static function unsetVal(&$arr,$unval){
        foreach($arr as $k => $v){
            if($v==$unval){
                unset($arr[$k]);
                break;
            }
        }
        return $arr;
    }

    /**
     * 将组合键名(以'.'连接)分解为数组
     * @param  string  $key            键名字串
     * @param  string  $AllowDot    是否允许包含连接符'.'
     */
    static function ParseKey($Key=''){
        $Key = preg_replace('/[^\w\.]/', '', (string)$Key);
        if($Key === '') return false;
        return explode('.',$Key);
    }
    
}
