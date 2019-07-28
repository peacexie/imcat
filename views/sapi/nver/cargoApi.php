<?php
namespace imcat;

class cargoApi extends bextApi{
    
    function homeAct(){
        $parts = read("cargo.i");
        $res['parts'] = $parts;
        foreach($parts as $kp => $vp) {
            $list = data('cargo', "catid='$kp'", 3, '');
            $res[$kp] = glbData::fmtList($list, 'cargo');
        }
        $res['row'] = $this->row;
        return $res;
    }

    function listAct(){
        // brand, price
        $res = $this->_list('cargo', self::expwhr());
        $res['list'] = glbData::fmtList($res['list'], 'cargo');
        $res['brands'] = $this->_tab('brand');
        $stab = explode(',', '10,100,200,300,500,800,1000');
        $prices = []; $prev = '';
        foreach($stab as $no=>$val){
            $nk = ($no==count($stab)-1) ? "$val~" : "$prev~$val";
            $nv = (!$prev) ? "{$val}元以下" : (($no==count($stab)-1) ? "{$val}元以上" : "$prev~{$val}元");
            $prices[] = ['kid'=>$nk, 'title'=>$nv];
            $prev = $val;
        }
        $res['prices'] = $prices;
        return $res;
    }

    function _detailAct(){
        $ops = [
            'hinfo' => ['type'=>'cOpt', 'mod'=>'hinfo',],
        ];
        $res['row'] = glbData::fmtRow($this->row, 'cargo', $ops);
        $temp = comStore::revSaveDir($res['row']['rel_pic']); 
        $ptmp = explode("\n", $temp); $pics = [];
        foreach ($ptmp as $key => $val) {
            $tmp = explode(",", str_replace(';','',trim($val)));
            $tmp[0] = vopCell::cPic($tmp[0], '', '160x120');
            $tmp[0] = glbData::fmtUrl($tmp[0]);
            $pics[$key] = $tmp;
        } 
        $res['pics'] = $pics;
        $rels = data('cargo', '', 4, '');
        $res['rels'] = glbData::fmtList($rels, 'cargo');
        return $res;
    }

    static function expwhr(){ 
        $whr = "";
        $brand = req('brand');
        $brand && $whr .= " AND brand='$brand'";
        $price = req('price');
        // price: price=~10, 300~500, 1000~
        $area = req('price');
        if(strstr($area,'~')){ //$area && 
            $arr = explode('~',$area);
            $arr[0] && $whr .= " AND price>='$arr[0]'";
            $arr[1] && $whr .= " AND price<='$arr[1]'";
        } 
        return $whr;
    }

}
