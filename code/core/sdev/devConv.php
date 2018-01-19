<?php


// ...数据转化类
class devConv{    


    static function rowId($arr=array()){
        // dtpic-2011-7X-M6KK.52SRH
        $kid = is_array($arr) ? strtolower(substr($arr['KeyID'],6,12)) : substr($arr,0,9).basKeyid::kidRand('24',3);
        $rec = db()->table("docs_cargo")->where("did LIKE '$kid%'")->find(); 
        if($rec) return self::rowId($kid);    
        else return $kid;
    }

    static function rowPbat($v15,$v16){
        $a15 = explode(',',$v15);
        $a16 = explode(',',$v16);
        $res = '';
        foreach ($a15 as $k1 => $v1) {
            if(!empty($v1) && !empty($a16[$k1])){
                $res .= (empty($res) ? '' : ';')."$v1:".$a16[$k1];
            }
        }
        return $res;
    }

    static function rowRelpic($kid){
        global $dba;
        $sql = " SELECT * FROM [InfoPhoto] WHERE KeyRe='$kid' ";
        $rs = $dba->query($sql);
        $res = '';
        foreach ($rs as $k => $row) {
            $img = $row['ImgName'];
            $img = str_replace("dtpic/","{oldUpic}/",$img);
            $res .= "$img;\n";
        }
        return $res;
    }

    static function rowContent($val,$kid){
        $res = str_replace('"/upfile/dtpic/',"{oldUpic}/",$val);
        return $res;
        //       ?KeyID=dtpic-2017-61-S29R.7ARYH
        //      /upfile/dtpic/2017/61/S29R.7ARYH/61SFQ1_FK7N0.jpg
        // src="/upfile/dtpic/2013/5G/GN6T.6Y9YQ/4Q2M2Q_SWFBS.jpg"
    }

    static function rowMpic($val,$kid){
        if(empty($val)) return '';
        $tmp = explode('^',$val);
        $res = str_replace("-","/",$kid).'/'.$tmp[1];
        $res = str_replace("dtpic/","{oldUpic}/",$res);
        return $res;
    }

    static function rowType($val){
        $tmp = explode(';',$val);
        if(strlen($val)>10){ // S210048;S220064;
            $val = $tmp[1];
        }else{
            $val = $tmp[0];
        }
        return strtolower($val);
    }

    static function c17Save1($res){
        $row = $res[1]; $ext = $res[2];
        $did = $res[0]; $aip = $row['aip'];
        $has = db()->table('docs_cargo')->where("aip='{$aip}'")->find();
        //dump($has);
        if(empty($has)){
            $row['did'] = $did;
            db()->table('docs_cargo')->data(in($row))->insert(0);
            $flag = 'Insert';
        }else{
            $did = $has['did']; 
            db()->table('docs_cargo')->data(in($row))->where("did='$did'")->update(0);
            $flag = 'Update';
        }
        if(!db()->table('dext_cargo')->where("did='{$did}'")->find()){
            $ext['did'] = $did;
            db()->table('dext_cargo')->data(in($ext))->insert(0);
        }else{
            db()->table('dext_cargo')->data(in($ext))->where("did='$did'")->update(0);
        }
        return "$flag:$did";
    }

    static function c17Row1($arr=array()){
        $did = self::rowId($arr); // func
        $ext = $row = array();
        $row['aip'] = $arr['KeyID']; // org
        // row
        $parrs = explode('^',$arr['InfPara']);
        $row['dno'] = 1;
        $row['catid'] = self::rowType($arr['InfType']); // tab
        $row['title'] = $arr['InfSubj'];
        $row['mpic'] = self::rowMpic($arr['ImgName'],$arr['KeyID']); 
        $row['guige'] = '';
        $row['xinghao'] = $arr['KeyCode']; 
        $row['price'] = floatval($parrs[4]); 
        $row['ordcnt'] = 1; 
        $row['weight'] = floatval($parrs[3]); 
        $row['click'] = $arr['SetRead']; 
        $row['show'] = $arr['SetShow']=='Y' ? 1 : 0;
        $row['atime'] = strlen($arr['LogATime'])>12 ? intval(strtotime($arr['LogATime'])) : 0; 
        $row['etime'] = strlen($arr['LogETime'])>12 ? intval(strtotime($arr['LogETime'])) : 0; 
        /*
        // did  dno catid   hinfo   brand   title   mpic    show    top color   click   
        // guige   xinghao price   ordcnt   weight  
        海关名-InfPara11(>haiguan)
        价格-InfPara4(>price)            原价-InfPara5(>pold)
        成本-b1Num(>porg)                批量价-InfPara15(16)(>pbat)
        重量-InfPara3(>weight),          假重-b1Wgt(>weight2)
        体积-InfPara12(>volume)          库存-InfPara8(>stock)
        免邮-SetFship(>freeship) 
        产地-???(>cfrom)                 厂商-InfPara2(>comp)
        赠送券-InfPara13(>youhui)        积分-InfPara14(>jifen)
        代购Url-b1Txt(>outurl)
        // did detail  author  source  seo_key seo_des seo_tag rel_pic rel_pro 
        // opt11   opt12   opt13   opt14   opt15   opt16   opt17   opt18   opt19   
        // pbat    haiguan pold    porg    weight2 volume  stock   freeship    
        // cfrom   comp    youhui  jifen   outurl
        */
        // ext --- $ext['xxx'] = $arr[''];
        $ext['detail'] = self::rowContent($arr['InfCont'],$arr['KeyID']); // root
        $ext['author'] = 'Import'; 
        $ext['source'] = ''; 
        $ext['rel_pic'] = self::rowRelpic($arr['KeyID']);
        $parr2 = devConv::c17Type2($arr['InfTyp2']);
        foreach($parr2 as $k2 => $v2) {
            $ext['opt'.(11+$k2)] = substr($v2['str'],0,240);
        }
        $ext['pbat'] =  self::rowPbat($parrs[15],$parrs[16]);
        $ext['haiguan'] = $parrs[11]; 
        $ext['pold'] = floatval($parrs[5]); 
        $ext['porg'] = $arr['b1Num'];
        $ext['weight2'] = floatval($arr['b1Wgt']);
        $ext['volume'] = intval($parrs[12]);
        $ext['stock'] = intval($parrs[8]);
        $ext['freeship'] = $arr['SetFship'];
        $ext['cfrom'] = 'China';
        $ext['comp'] = $parrs[2];
        $ext['youhui'] = $parrs[13];
        $ext['jifen'] = intval($parrs[14]);
        $ext['outurl'] = $arr['b1Txt'];
        // $a1 = 
        return array($did,$row,$ext);
    }

    static function c17Type2($InfTyp2=''){
        $re = array();
        $ar1 = explode('^', $InfTyp2);
        $i = 1;
        for($i=1;$i<120;$i=$i+5){
            if($i>=count($ar1)) break;
            $itm = array();
            $its = $itm['title'] = $ar1[$i];
            $b2 = explode('@', @$ar1[$i+1]); // opt
            $b3 = explode('@', @$ar1[$i+2]); // price
            $b4 = explode('@', @$ar1[$i+3]); // weight
            $b5 = explode('@', @$ar1[$i+4]); // vol
            $ops = array();
            foreach ($b2 as $k2 => $v2) {
                if(!empty($v2)){
                    $ops[$k2]['name'] = $v2;
                    $ops[$k2]['paras'] =     (empty($b3[$k2]) ? 0 : $b3[$k2]).","; 
                    $ops[$k2]['paras'] .= "".(empty($b4[$k2]) ? 0 : $b4[$k2]).","; 
                    $ops[$k2]['paras'] .= "".(empty($b5[$k2]) ? 0 : $b5[$k2]).",";
                    $its .= "^$v2@{$ops[$k2]['paras']}";
                }
            }
            if(!empty($ops)){
                $itm['ops'] = $ops;
                $itm['str'] = $its;
                $re[] = $itm;
            }
        }
        return $re;
    }


    static function c17rTypes(){
        $data = "
<option value='S210025;'>        ├─EU No Tax Free shipping</option>
<option value='S210028;'>        ├─US  No Tax Free shipping</option>
<option value='S210032;'>        ├─Mid Motor and parts</option>
<option value='S210032;S220008;'>│ ├─Torque sensor mid motor</option>
<option value='S210032;S220012;'>│ ├─8FUN Mid-Drive Motor Kits</option>
<option value='S210032;S220020;'>│ ├─8FUN Mid Motor Parts</option>
<option value='S210042;'>        ├─Ebike battery</option>
<option value='S210042;S220072;'>│ ├─Panasonic cell ebike battery</option>
<option value='S210042;S220076;'>│ ├─SANYO cell ebike battery</option>
<option value='S210042;S220084;'>│ ├─Samsung LG cell ebike battery</option>
<option value='S210042;S220092;'>│ ├─Charger</option>
<option value='S210048;'>        ├─Controller</option>
<option value='S210048;S220052;'>│ ├─Sine Wane</option>
<option value='S210048;S220056;'>│ ├─Torque Simulation</option>
<option value='S210048;S220060;'>│ ├─LCD/LED Display</option>
<option value='S210048;S220064;'>│ ├─Parts of Controller</option>
<option value='S210052;'>        ├─Inverter</option>";
        preg_match_all("/value=\'([^']+)[^\w]+([^<]+)/", $data, $ms);
        foreach ($ms[1] as $no=>$key) {
            $val = $ms[2][$no];
            $kid = self::rowType($key);
            echo "$key $val<br>\n";
            $row = array(
                'kid' => $kid,
                'model' => 'cargo', 'enable' => 1, 'frame' => 0, 
                'pid' => strlen($key)>10 ? strtolower(substr($key,0,7)) : '0',
                'title' => $val,
                'deep' => strlen($key)>10 ? '2' : '1',
            );
            if(!db()->table('base_catalog')->where("kid='$kid'")->find()){
                db()->table('base_catalog')->data($row)->insert();
            }
            dump($row);
            // rowType($val) base_catalog_ys
            //(`kid`, `model`, `pid`, `title`, , `enable`, `deep`, `frame`, `char`, ) 
            //('p2014',   'cargo',    'p1012',    '手机配件',    1,  2,  0,  'C');
        }
        //dump($ms);
    }

}
