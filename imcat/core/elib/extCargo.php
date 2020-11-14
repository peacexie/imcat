<?php
namespace imcat;

// extCargo

class extCargo{

    #static $enkey = '2~B`y^';
    
    function __construct() {
        //$;
    }

    // 图片数组
    static function pics($pics, $root=1){
        $pics = comStore::picsTab($pics);
        $host = (basEnv::isHttps()?'https':'http').'://'.basEnv::serval('host', '127.0.0.1');
        foreach($pics as $ik=>$img) {
            $pics[$ik] = $host.$img;
        }
        return $pics;
    }

    // 取一个key对应的产品模型名称
    static function keyUmod($umod, $def='{key}'){
        $res = '';
        $row = db()->table('exd_umod')->where("kid='$umod'")->find();
        if(!empty($row)){
            $res = $row['title'];
        }else{
            $res = $def=='{key}' ? $key : $def;
        }
        return $res;
    }
    // 取一个key对应的类别名称
    static function keyType($mod, $key, $def='{key}'){
        $res = '';
        $tab = read("$mod.i"); 
        if(isset($tab[$key])){
            $res = $tab[$key]['title'];
        }else{
            $res = $def=='{key}' ? $key : $def;
        }
        return $res;
    }
    // 取一个属性
    static function oneAttr($atts, $keys, $def=''){
        $tmp = explode(',', $keys);
        $res = '';
        foreach($tmp as $tk) {
            if(!$tk){ continue; }
            if(isset($atts[$tk])){
                $res .= ($res?",":'').$atts[$tk];
            }
        }
        return $res ? $res : $def;
    }

    // 产品字段中的属性
    static function fieldAtts($row, $rearr=0){
        if(is_string($row)){
            $attstr = $row;
        }else{
            $attstr = $row['attcom'];
            if(isset($row['attso'])){ $attstr = $row['attso']."\n$attstr"; };
        }
        preg_match_all("/([^\n\r]+)\=\`([^\`]*)\`/i", $attstr, $itms);
        $res = []; 
        foreach($itms[1] as $no=>$key){
            $val = $itms[2][$no]; // ,木头,铁,
            $arr = array_filter(explode(',', $val));
            $val = $rearr ? $arr : implode(',', $arr);
            $res[$key] = $val;
        } 
        return $res;
    }

    // 产品模型中的属性
    static function umodAtts($row, $gbuy=0){ 
        $fatts = self::fieldAtts($row);
        $res = [];
        $umod = $row['attmod'];
        $data = db()->table('exd_uatt')->where("pid='$umod'")->order('top')->select();
        foreach($data as $kno => &$vr){
            $akey = $vr['title'];
            if(!isset($fatts[$akey])){ // TODO:规定显示
                $aval = '(暂无)';
            }elseif($fatts[$akey]=='(del)'){ // TODO:规定忽略
                continue;
            }else{
                $aval = $fatts[$akey];
            }
            if(!$gbuy){
                $gkey = empty($vr['gkey']) ? '(未分组)' : $vr['gkey'];
                $iatt = ['name'=>$akey, 'value'=>$aval, 'gkey'=>$gkey];
            }else{
                $iatt = ['attributeID'=>$vr['kid'], 'attributeName'=>$akey, 'valueID'=>$umod.$vr['kid'], 'value'=>$aval];
            }
            $res[] = $iatt;
        }
        return $res;
    }
    // 产品模型属性分组(group分组)
    static function umodGtab($umod, $atts=[]){ 
        $umod = is_array($umod) ? $umod['attmod'] : $umod;
        $mrow = db()->table('exd_umod')->where("kid='$umod'")->find();
        $gtab = [];
        if(!empty($mrow)){
            $gtab = basElm::line2arr($mrow['gtab']);
            $gtab[] = '(未分组)';
        }
        return $gtab;
    }

    // 产品关联的配件
    static function relParts($row, $rkey=''){ // re:ware
        $pid = $row['did'];
        $ware = '主产品 x1';
        $data = db()->table('exd_upart')->where("pid='$pid'")->order('top')->select(); 
        if($data){
            foreach($data as $kno => &$vr){
                unset($vr['aip'],$vr['atime'],$vr['auser'],$vr['eip'],$vr['etime'],$vr['euser']);
                $ware .= ', '.str_replace('配件', '', $vr['title']).' x1';
            }
            $cnt = count($data);
        }else{
            $cnt = 0;
        }
        $res = ['cnt'=>$cnt, 'list'=>$data, 'ware'=>$ware];
        if($rkey){ return $res[$rkey]; }
        return $res;
    }

    // 产品的标准参数
    static function attsParam($row){ 
        $fp = DIR_VARS."/gbatts/$row[apino].htm";
        if(file_exists($fp)){
            $stdata = file_get_contents(DIR_VARS."/gbatts/$fp.htm");
        }else{
            $stdata = self::attsGbuytab($row);
        }
        $stdata = str_replace(["\r","\n"], ['',''], $stdata);
        return $stdata;
    }

    static function attsGbuytab($row){
        // 主体参数
        $paras = self::umodAtts($row, 1);
        $str = "<table class='Ptable' id='info'>".
            "<tbody><tr><th colspan='2'>主体参数</th></tr>";
        foreach ($paras AS $rk=>$rv){
            $str .= "<tr><td class='tdTitle'>$rv[attributeName]</td><td>$rv[value]</td></tr>";
        }
        $str .= "</tbody></table>";
        // 配件参数
        $parts = self::relParts($row, 'list');
        $str .= "<table class='Ptable' id='parts'>".
            "<tbody><tr><th colspan='2'>配件参数</th></tr>";
        if(!empty($parts['cnt'])){
            foreach ($parts as $rp) { 
                $str .= "<tr><td><table class='Ptable'><tbody>";
                $atts = self::fieldAtts($rp);
                foreach ($atts AS $rk=>$rv){
                    $str .= "<tr><td class='tdTitle'>$rk</td><td>$rv</td></tr>";
                }
                $str .= "</tbody></table></td></tr>";
            }
        }
        $str .= "</tbody></table>";
        // 服务参数
        $str.= "<table class='Ptable' id='servers'>".
            "<tbody><tr><th colspan='2'>服务参数</th></tr>"; 
        $str .= "<tr><td><table class='Ptable'><tbody>";
        if(!empty($row['adrem'])){
            $atts = self::fieldAtts($row['adrem']);
            foreach ($atts AS $rk=>$rv){
                $str .= "<tr><td class='tdTitle'>$rk</td><td>$rv</td></tr>";
            }        
        }else{
            $str .= "<tr><td class='tdTitle'>售后服务</td><td>$row[serv]</td></tr>";
        }
        $str .= "</tbody></table></td></tr>";
        $str .= "</tbody></table>";
        return $str;
    }

}

/*

*/
