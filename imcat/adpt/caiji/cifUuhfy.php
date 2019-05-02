<?php
namespace imcat;

#caiji interface for Uuhaofang

class cifUuhfy{

    public $skcfg = array();
    public $db = null;
    public $hxtab = ['ROOM'=>'室','HALL'=>'厅','WEI'=>'卫','CHU'=>'厨','YANG'=>'阳台',];

    static function fixFy($type='sale'){
        $local = caiBase::attrs('local.i'); 
        $cnt = 0; 
        $list = db()->table('docs_'.$type)->where("lpid LIKE 'hft-%'")->limit(50)->select();
        if($list){ 
            foreach($list as $row) {
                $upd1 = []; $tmp = explode('/',str_replace(',','/',$row['addr']));
                if(!empty($tmp[2])){
                    $old = db()->table('docs_house')->where("title='{$tmp[0]}'")->find();
                    if(!$old){
                        $upd1['title'] = $tmp[0];
                        $upd1['catid'] = 'xq';
                        $upd1['areas'] = caiBase::atkeys($local['0'],$tmp[1],2);
                        $upd1['addr'] = $tmp[1].$tmp[2];
                        $upd1['wytype'] = $row['wytype'];
                        $upd1['zxtype'] = $row['zxtype'];
                        $upd1['exid'] = $row['lpid'];
                        $kar = [caiBase::dbKid('docs_house',$row['dno']),$row['dno']];
                        $upd1['did'] = $kar[0]; $upd1['dno'] = $kar[1];
                        $upd2['lpid'] = $upd1['did']; //dump($upd1);
                        db()->table('docs_house')->data($upd1)->insert();
                    }else{
                        $upd2['lpid'] = $old['did'];
                    }
                }else{
                    $upd2['lpid'] = '';
                }
                db()->table('docs_'.$type)->where("did='{$row['did']}'")->data($upd2)->update();
                $cnt++;
            } 
        }
        return $cnt;
    }

    function __construct($cfgs){
        $this->skcfg = $cfgs;
        comHttp::setCache(3);
    }

    public function pageData($url){
        $data = comHttp::fileCrawl($url); 
        // dump("$url\n$data");
        $sum = json_decode($data,1); 
        if(!empty($sum['ERRCODE'])){
            die('('.$sum['ERRCODE'].')'.$sum['ERRMSG']);
        }
        $data = $sum['DATA']; unset($sum['DATA']);
        $list = []; 
        if(!empty($data)){
            foreach($data as $row){
                unset($row['RICH_TEXT']);
                $mdks = ['PHOTO_ARRAY','VEDIO_INFO'];
                foreach($mdks as $mk) {
                    if(!empty($row[$mk])){
                        $row[$mk] = json_decode($row[$mk],1);
                    }
                }
                $list[] = $row;
            }
        }
        return ['sum'=>$sum, 'list'=>$list];
    }

    public function saleRow($row, $no=12){ 
        $fang = caiBase::attrs('fang.i'); 
        $local = caiBase::attrs('local.i'); 
        $info = $ext = [];
        $reinfo = "";
        // row > info 
        $info['exid'] = 'hft-'.$row['SALE_ID'];
        $info['title'] = $row['SALE_SUBJECT'];
        $info['catid'] = 'sysfy';
        $info['mpic'] = empty($row['THUMB_URL']) ? '' : $row['THUMB_URL'];
        $info['pall'] = $row['SALE_TOTAL_PRICE'];
        $info['price'] = $row['SALE_UNIT_PRICE'];
        $info['lpid'] = 'hft-'.$row['BUILD_ID'];
        //$info['map'] = $res1['map'];
        $info['hxs'] = '';
        foreach($this->hxtab as $hk => $hv) {
            $info['hxs'] .= (empty($row['SALE_'.$hk]) ? 0 : $row['SALE_'.$hk]).$hv;
        }
        $info['hxroom'] = empty($row['SALE_ROOM']) ? 0 : $row['SALE_ROOM'];
        $info['mjout'] = $row['SALE_AREA'];
        if(!empty($row['SALE_INNERAREA'])) $info['mjin'] = $row['SALE_INNERAREA'];
        $info['cxtype'] = caiBase::atkeys($fang['cx'],$row['SALE_DIRECT'],2);
        $info['zxtype'] = caiBase::atkeys($fang['zx'],$row['SALE_FITMENT'],2);
        $info['wytype'] = caiBase::atkeys($fang['wy'],$row['SALE_USEAGE'],2);
        $info['jztype'] = caiBase::atkeys($fang['jz'],$row['SALE_STRUCT'],2); // ??
        $tags = empty($row['SALE_STREET']) ? '' : '临街'.',';
        $tags = empty($row['SALE_TYPE']) ? '' : $row['SALE_TYPE'].',';
        $info['sotags'] = $tags.$row['TAG_IDS']; 
        $info['addr'] = $row['BUILD_NAME'].'/'.$row['REGION_NAME'].','.$row['SECTION_NAME'].','.$row['TRADE_ADDR']; 
        // data > ext
        $ext['tihu'] = $row['SALE_LADDER'].'梯/'.@$row['SALE_DOORS'].'户';
        $ext['louceng'] = @$row['SALE_FLOOR'].'层/共'.@$row['SALE_FLOORS'].'层';
        $ext['cqnian'] = $row['SALE_RIGHT']=='产权' ? '-' : $row['SALE_RIGHT']; 
        $ext['jznian'] = $row['SALE_YEAR']; 
        $ext['wyfee'] = empty($row['SALE_INNERAREA']) ? 0 : $row['SALE_PROPERTY'];
        $ext['remark'] = $row['SALE_CHARACT'];
        $ext['remzb'] = '';
        $ext['remjt'] = '';
        $rem = 'NO:'.$row['SALE_NO'].";\n";
        $rem = empty($row['USER_NAME']) ? '' : '联络:'.$row['USER_NAME'].',';
        $rem .= empty($row['USER_MOBILE']) ? '' : '电话:'.$row['USER_MOBILE'].";\n";
        $ext['remin'] = $rem; // ??
        if(!empty($row['VEDIO_INFO'])){
            $tmp = $row['VEDIO_INFO'];
            $ext['videos'] = $tmp['VIDEO_ADDR']."(,)".$tmp['PHOTO_ADDR']."(;)\n";
        }
        // update
        $old = db()->table('docs_sale')->where("exid='{$info['exid']}'")->find();
        if(!$old){
            $kar = [caiBase::dbKid('docs_sale',$no),$no];
            $lpid = $info['did'] = $ext['did'] = $kar[0];
            $info['dno'] = $kar[1];
            db()->table('docs_sale')->data($info)->insert();
            $reinfo .= "### {$info['exid']} > {$info['did']} ins; <br>\n";
            $actf = 'ins';
        }else{
            $lpid = $ext['did'] = $old['did'];
            db()->table('docs_sale')->data($info)->where("did='{$old['did']}'")->update();
            $reinfo .= "### {$info['exid']} > {$old['did']} upd; <br>\n";
            $actf = 'upd';
        }
        db()->table('dext_sale')->data($ext)->replace();
        // pics
        $cabi = $cabu = 0;
        if(!empty($row['PHOTO_ARRAY'])){
            foreach($row['PHOTO_ARRAY'] as $tr){ 
                $abi = ['title'=>basename($tr['PHOTO_ADDR']),'mpic'=>$tr['PHOTO_ADDR'],'aflag'=>'8','atype'=>'',];
                $abi['lpid'] = $lpid;
                $abi['part'] = 'sale';
                $abi['exid'] = "hft-".(md5($tr['PHOTO_ADDR'])); 
                $old = db()->table('docs_album')->where("exid='{$abi['exid']}'")->find();
                if(!$old){
                    $kar = [caiBase::dbKid('docs_album',$cabi,0,$no),$cabi];
                    $abi['did'] = $kar[0];
                    $abi['dno'] = $kar[1];
                    db()->table('docs_album')->data($abi)->insert();
                    $reinfo .= "- album {$abi['exid']} > {$abi['did']} ins; <br>\n";
                    $cabi++;
                }else{
                    db()->table('docs_album')->data($abi)->where("did='{$old['did']}'")->update();
                    $reinfo .= "- album {$abi['exid']} > {$old['did']} upd; <br>\n";
                    $cabu++;
                }
            }
        }
        // debug
        #dump($info);
        #dump($ext);
        // return
        $res = ['actf'=>$actf,'reinfo'=>$reinfo,'cabi'=>$cabi,'cabu'=>$cabu,];
        return $res;
    }

    public function rentRow($row, $no=12){ 
        $fang = caiBase::attrs('fang.i'); 
        $local = caiBase::attrs('local.i'); 
        $info = $ext = [];
        $reinfo = "";
        // row > info 
        // row > info 
        $info['exid'] = 'hft-'.$row['LEASE_ID'];
        $info['title'] = $row['LEASE_SUBJECT'];
        $info['catid'] = 'sysfy';
        $info['mpic'] = empty($row['THUMB_URL']) ? '' : $row['THUMB_URL'];
        //$info['pall'] = $row['SALE_TOTAL_PRICE'];
        $info['price'] = $row['LEASE_TOTAL_PRICE'];
        $info['lpid'] = 'hft-'.$row['BUILD_ID'];
        //$info['map'] = $res1['map'];
        $zutps = ['整租'=>'1','合租'=>'2','短租'=>'3','未知'=>'4','xxx'=>'4',];
        $info['zutype'] = caiBase::atkeys($fang['cx'],$row['LEASE_ACCOUNT'],2); // ???
        $info['hxs'] = '';
        foreach($this->hxtab as $hk => $hv) {
            $info['hxs'] = (empty($row['LEASE_'.$hk]) ? 0 : $row['LEASE_'.$hk]).$hv;
        }
        $info['hxroom'] = empty($row['LEASE_ROOM']) ? 0 : $row['LEASE_ROOM'];
        $info['mjout'] = $row['LEASE_AREA'];
        if(!empty($row['LEASE_INNERAREA'])) $info['mjin'] = $row['LEASE_INNERAREA']; // ??
        $info['cxtype'] = caiBase::atkeys($fang['cx'],$row['LEASE_DIRECT'],2);
        $info['zxtype'] = caiBase::atkeys($fang['zx'],$row['LEASE_FITMENT'],2);
        $info['wytype'] = caiBase::atkeys($fang['wy'],$row['LEASE_USEAGE'],2);
        //$info['jztype'] = caiBase::atkeys($fang['jz'],$row['LEASE_STRUCT'],2); // ??
        $tags = empty($row['LEASE_STREET']) ? '' : '临街'.',';
        $tags = empty($row['LEASE_TYPE']) ? '' : $row['LEASE_TYPE'].',';
        $info['sotags'] = $tags.(empty($row['TAG_IDS'])?'':$row['TAG_IDS']); 
        $info['addr'] = $row['BUILD_NAME'].'/'.$row['REGION_NAME'].','.$row['SECTION_NAME'].','.$row['TRADE_ADDR']; 
        // data > ext 
        $ext['yaqi'] = '押金:'.@$row['LEASE_DEPOSIT'].'/期限:'.$row['LEASE_LIMITE'].'/结算:'.$row['LEASE_ACCOUNT'];
        $ext['tihu'] = @$row['LEASE_LADDER'].'梯/'.@$row['LEASE_DOORS'].'户';
        $ext['louceng'] = @$row['LEASE_FLOOR'].'层/共'.@$row['LEASE_FLOORS'].'层';
        //$ext['cqnian'] = $row['LEASE_RIGHT']=='产权' ? '-' : $row['LEASE_RIGHT']; 
        $ext['jznian'] = $row['LEASE_YEAR']; 
        //$ext['wyfee'] = $row['LEASE_PROPERTY'];
        if(!empty($row['LEASE_SET'])){
            $sets = str_replace(' ', ',', $row['LEASE_SET']);
            $ext['peitao'] = caiBase::atkeys($fang['pt'],$sets,2);
        }
        $ext['remark'] = $row['LEASE_CHARACT'];
        $ext['remzb'] = '';
        $ext['remjt'] = '';
        $rem = 'NO:'.$row['LEASE_NO'].";\n";
        $rem = empty($row['USER_NAME']) ? '' : '联络:'.$row['USER_NAME'].',';
        $rem .= empty($row['USER_MOBILE']) ? '' : '电话:'.$row['USER_MOBILE'].";\n";
        $ext['remin'] = $rem; // ??
        if(!empty($row['VEDIO_INFO'])){
            $tmp = $row['VEDIO_INFO'];
            $ext['videos'] = $tmp['VIDEO_ADDR']."(,)".$tmp['PHOTO_ADDR']."(;)\n";
        }
        // update
        $old = db()->table('docs_rent')->where("exid='{$info['exid']}'")->find();
        if(!$old){
            $kar = [caiBase::dbKid('docs_rent',$no),$no];
            $lpid = $info['did'] = $ext['did'] = $kar[0];
            $info['dno'] = $kar[1];
            db()->table('docs_rent')->data($info)->insert();
            $reinfo .= "### {$info['exid']} > {$info['did']} ins; <br>\n";
            $actf = 'ins'; 
        }else{
            $lpid = $ext['did'] = $old['did'];
            db()->table('docs_rent')->data($info)->where("did='{$old['did']}'")->update();
            $reinfo .= "### {$info['exid']} > {$old['did']} upd; <br>\n";
            $actf = 'upd';
        }
        db()->table('dext_rent')->data($ext)->replace();
        // pics
        $cabi = $cabu = 0;
        if(!empty($row['PHOTO_ARRAY'])){
            foreach($row['PHOTO_ARRAY'] as $tr){ 
                $abi = ['title'=>basename($tr['PHOTO_ADDR']),'mpic'=>$tr['PHOTO_ADDR'],'aflag'=>'8','atype'=>'',];
                $abi['lpid'] = $lpid;
                $abi['catid'] = 'rent';
                $abi['exid'] = "hft-".(md5($tr['PHOTO_ADDR'])); 
                $old = db()->table('docs_album')->where("exid='{$abi['exid']}'")->find();
                if(!$old){
                    $kar = [caiBase::dbKid('docs_album',$cabi,0,$no),$cabi];
                    $abi['did'] = $kar[0];
                    $abi['dno'] = $kar[1];
                    db()->table('docs_album')->data($abi)->insert();
                    $reinfo .= "- album {$abi['exid']} > {$abi['did']} ins; <br>\n";
                    $cabi++;
                }else{
                    db()->table('docs_album')->data($abi)->where("did='{$old['did']}'")->update();
                    $reinfo .= "- album {$abi['exid']} > {$old['did']} upd; <br>\n";
                    $cabu++;
                }
            }
        }
        // debug
        #dump($info);
        #dump($ext);
        // return
        $res = ['actf'=>$actf,'reinfo'=>$reinfo,'cabi'=>$cabi,'cabu'=>$cabu,];
        return $res;
    }

    // 
    public function apiLast($last=''){
        $logkey = "{$this->type}";
        if($last=='clear'){ // clear-1
            $this->db->table('cache')->where("kid='$logkey'")->delete();
        }elseif(!empty($last)){ // save
            $data = array('kid'=>$logkey,'val'=>$last);
            $this->db->table('cache')->data($data)->replace();
        }elseif($this->run=='cmd'){ // read
            $row = $this->db->table('cache')->where("kid='$logkey'")->find();
            $last = $row ? $row['val'] : '2015-01-01'; 
        }else{ // web 下使用
            $last = req('last', '2015-01-01'); // 2015-01-01 / 2017-05-18%2019:43:53.953
        }
        return $last;
    }
    
    // type: Sale, Lease, Comp, User, Dept
    public function apiUrl($type, $last=''){
        $apiType = ucfirst($type);
        $slast = ""; 
        if($last && in_array($type,array('Sale','Lease'))){
            $key = "LAST_TIME_".strtoupper($type);
            $slast = "&$key=".str_replace(' ','%20',$last); //($last ?  : '2015-01-01');
        }else{
            $slast = "";
        }
        $url = "http://user.haofang.net/hftWebService/web/openApi/data/get{$apiType}List?";
        $param = "COMP_NO={$this->skcfg['ak']}&SYNC_VERIFYID={$this->skcfg['as']}$slast";
        if($apiType=='Comp'){ $url = str_replace('List?','Info?',$url); }
        return $url.$param;
    }

    // https://linzhi.haofang.net/sale/
    public function syncArea(){
        ; 
    }

    // --------------------------------------------

    // 缩略图
    static function thumb($url, $mw, $mh, $def='demo_error.jpg')
    {
        if(empty($url)){
            return PATH_STATIC."/icons/basic/$def";
        }
        return "$url?x-oss-process=image/resize,m_fill,h_$mw,w_$mh";
        // return "{$base}_{$mw}x{$mh}$ext";
    }

}

/*

*/
