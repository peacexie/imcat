<?php
namespace imcat;

// dopSwap : 数据交换(导入导出) 
// for : 
class dopSwap{    

    public $cfg = array();

    static $exfields = [
        'catid' => [
            'kid' => 'catid',
            'title' => '栏目',
            'etab' => '0',
            'type' => 'select',
            'dbtype' => 'varchar',
            'dblen' => '12',
            'top' => '0',
            'fmextra' => '',
            'cfgs' => '', // $mod
        ],  
        'atime' => [
            'kid' => 'atime',
            'title' => '添加时间',
            'etab' => '0',
            'type' => 'input',
            'dbtype' => 'int',
            'dblen' => '10',
            'top' => '99999',
            'fmextra' => 'datetm',
            'fmexstr' => 'Y-m-d H:i',
            'cfgs' => '',
        ],  
        'etime' => [
            'kid' => 'etime',
            'title' => '修改时间',
            'etab' => '0',
            'type' => 'input',
            'dbtype' => 'int',
            'dblen' => '10',
            'top' => '99999',
            'fmextra' => 'datetm',
            'fmexstr' => 'Y-m-d H:i',
            'cfgs' => '',
        ],
    ];

    static $exfimp = [
        '栏目' => 'catid',
    ];

    //function __destory(){  }
    function __construct($cfg, $tabid=''){ 
        $this->cfg = $cfg;
    }
    
    # === imp-data start =======================================

    // Excel数据 -> 保存db
    static function xlsDimp($fp, $mod, $unk, $must, $rtb=0){
        //$pos = strpos($upres['url'], "/@udoc/");
        //$fp = DIR_DTMP.substr($upres['url'], $pos);
        $fields = self::exFcfg($mod, ['catid','atime','etime']);
        $data = extExcel::exRead($fp, 'utf-8', $rtb);
        if(empty($data['numRows']) || $data['numRows']<2){
            return ['errno'=>'Empty-Data', 'errmsg'=>'数据为空'];
        }
        $hda = $data['cells'][1]; // 表头: 1-姓名
        $hdb = []; // 表头下标对应字段名: 1-name
        $mkey = []; // 不能为空的字段: ['1','2']
        $skip_fids = [];
        foreach($hda as $no=>$fname){
            $flag = 0;
            foreach($fields as $fk=>$cfg){
                if($cfg['title']==$fname){ 
                    $hdb[$no] = $fk; $flag = 1;
                    if(!empty($must) && in_array($fk,$must)){ $mkey[]=$no; }
                    break;
                }
            }
            if(!$flag){ $skip_fids[] = $fname; }
        } //dump($mkey); dump($hda); dump($hdb);
        // numRows
        $res = ['ins'=>0, 'upd'=>0, 'itms'=>[], 'skip_fids'=>$skip_fids, 'skip_rows'=>[]];
        for($i=2; $i<=$data['numRows']; $i++){
            $r0 = $data['cells'][$i]; // 原始数据
            $r1 = []; // 字段健-字段值
            $mcnt = 0; // 必填字段计数
            foreach($r0 as $no=>$v0){
                if(!isset($hdb[$no])){ continue; } // 过滤字段
                $key = $hdb[$no];
                $r1[$key] = $v0; 
                if(in_array($no,$mkey)){ $mcnt++; }
            } 
            if($mcnt<count($mkey)){ 
                $res['skip_rows'][] = "skip: " . implode(', ', $r0);
                continue; 
            } // 过滤行
            // dump($r1);
            $rdb = self::rowSvfmt($mod, $r1, $fields, $unk);
            $rex = "$rdb"; $tks = explode(',', implode(',',$unk).','.implode(',',$must));
            foreach($tks as $fk){ 
                if(strpos($rex," : $r1[$fk]")){ continue; }
                $rex .= " : $r1[$fk]";
            }
            $res['itms'][] = $rex;
            if($rdb=='ins'){
                $res['ins']++;
            }else{
                $res['upd']++;
            }
        }
        return $res; //$data;

    }
    // 反解析一行数据-并保存到数据库
    static function rowSvfmt($mod, $r1, $fields, $unk){
        //$row = []; //dump($row);
        $fmrow = $exrow = [];
        if(is_string($fields)){ $fields = read("$fields.f"); } 
        foreach($fields as $fk=>$fv){
            if($fv['dbtype']=='nodb'){ continue; }
            $kid = $fv['kid']; 
            $val = isset($r1[$kid]) ? $r1[$kid] : (strstr($fv['dbtype'],'int') ? '0' : ''); 
            // 
            if($val && in_array($fv['type'],['select','cbox','radio'])){
                $arr = basElm::text2arr($fv['cfgs']); //dump($arr);
                foreach($arr as $ak=>$av){
                    if($val==$av){ $val=$ak; break; }
                }
            }elseif($val && $fv['fmextra']=='datetm'){
                $fmt = empty($fv['fmexstr']) ? 'Y-m-d' : $fv['fmexstr'];
                if($fv['dbtype']=='int'){
                    $val = is_numeric($val) ? $val : strtotime($val);
                }else{
                    $val = is_numeric($val) ? date($fmt,$val) : $val;
                }
            }
            if(!empty($fv['etab'])){
                $exrow[$kid] = $val;
            }else{
                $fmrow[$kid] = $val;
            }
        } //dump($exrow); dump($fmrow); die();
        $whr = ''; $una = is_array($unk) ? $unk : [$unk];
        foreach($una as $fk){
            $whr .= ($whr?' AND ':'') . "$fk='$r1[$fk]'";
        }
        $dbtab = glbDBExt::getTable($mod); 
        $dbext = glbDBExt::getTable($mod, 1);
        $dbk = substr($dbtab,0,1).'id'; $flag = '';
        $hasf = db()->table($dbtab)->where($whr)->find();
        if(empty($hasf)){
            $kar = glbDBExt::dbAutID($dbtab);
            $d0 = [$dbk=>$kar[0], substr($dbtab,0,1).'no'=>$kar[1]]; 
            db()->table($dbtab)->data($d0+$fmrow)->insert();
            if(!empty($exrow)){
                
                db()->table($dbext)->data([$dbk=>$kar[0]]+$exrow)->insert();
            }
            $flag = "ins";
        }else{
            unset($fmrow['atime'],$fmrow['etime']);
            db()->table($dbtab)->data($fmrow)->where($whr)->update();
            if(!empty($exrow)){
                db()->table($dbext)->data($exrow)->where([$dbk=>$hasf[$dbk]])->update();
            }
            $flag = 'upd';
        }
        return $flag;
    }

    # === imp-data end =======================================

    // 输出字段配置到Excel
    static function outMdemo($mod, $whr='', $ord='', $top=1, $exfids=[]){
        // xxx
        $exfids = []; //['catid','atime','etime']; // 测试
        $res1 = self::outMarr($mod, $whr, $ord='', $top, $exfids);
        $res2 = self::outFcfg($mod); // ($head, $data, 1);
        // 
        $head = array($res1['head'], $res2['head']);
        $data = array($res1['data'], $res2['data']); 
        #dump($head); dump($data);
        $fdata = extExcel::exWrite($head, $data, 2, 'utf-8', "{$mod}-Demo-");
    }

    // 输出模型数据(Excel格式使用)
    static function outMarr($mod, $whr='', $ord='', $top=1, $exfids=[], $fields=[]){
        // fields
        $fields = empty($fields) ? self::exFcfg($mod,$exfids) : $fields;
        $head = []; //$fkeys = [];
        foreach($fields as $fk=>$fv){
            if($fv['dbtype']=='nodb'){ continue; }
            $kid = $fv['kid']; 
            $head[$kid] = $fv['title'];
        }
        // data
        $atmp = data("$mod.join", $whr, $top, $ord); 
        $list = ($top==1) ? [$atmp] : $atmp;
        foreach ($list as $r1) {
            $data[] = self::rowParse($r1, $fields);
        }
        // 
        return ['head'=>$head, 'data'=>$data];
    }

    // 输出字段配置(Excel格式使用)
    static function outFcfg($mod){
        $groups = read('groups');
        $fields = db()->table('base_fields')->where("model='$mod' AND enable='1'")->order('top,kid')->select(); 
        $head = [
            '1' => '',
            '2' => $mod, 
            '3' => '',
            '4' => '',
            '5' => '',
            '6' => '',
            '7' => $groups[$mod]['title'],
            '8' => '',
        ];
        $data = []; $no = 1;
        foreach($fields as $fk => $fv) {
            $cfgs = $fv['cfgs']; $vkey = $vmsg = '';
            if(isset($groups[$cfgs])){
                $vkey = "$cfgs.i";
                $tab = read($vkey);
                foreach($tab as $k1 => $v1) {
                    $vmsg .= ($vmsg ? "、" : '').$v1['title'];
                }
            }elseif(strpos($cfgs,'=') && (strpos($cfgs,"\n") || strpos($cfgs,"\r"))){
                $tab = basElm::text2arr($cfgs); //dump($tab);
                foreach($tab as $k1 => $v1) {
                    $vkey .= ($vkey ? "、" : '').$k1;
                    $vmsg .= ($vmsg ? "、" : '').$v1;
                }
            }
            $row[1] = $fv['top'];
            $row[2] = $fv['kid'];
            $row[3] = $fv['etab'];
            $row[4] = $fv['type'];
            $row[5] = $fv['dblen'];
            $row[6] = $vkey;
            $row[7] = $fv['title'];
            $row[8] = $fv['fmextra']=='datetm' ? 'datetm' : $vmsg;
            $row[9] = '.';
            $data[$no] = $row;
            $no++;
        }
        return ['head'=>$head, 'data'=>$data];
        //$fdata = extExcel::exWrite($head, $data, 1);
    }

    # ==========================================

    // 模型字段+扩展字段
    static function exFcfg($mod, $exfids=[]){
        $mflds = read("$mod.f"); 
        $fields = $mflds; 
        if(in_array('catid',$exfids)){
            $fcatid = self::$exfields['catid']; $fcatid['cfgs'] = $mod;
            $fields = ['catid'=>$fcatid] + $mflds;
        }
        if(in_array('atime',$exfids)){
            $fields = $fields + ['atime'=>self::$exfields['atime']];
        }
        if(in_array('etime',$exfids)){
            $fields = $fields + ['etime'=>self::$exfields['etime']];
        } 
        return $fields; 
    }
    // 解析一行数据
    static function rowParse($r1, $fields, $kfix=''){
        $row = []; //dump($row);
        if(is_string($fields)){ $fields = read("$fields.f"); } 
        foreach($fields as $fk=>$fv){
            if($fv['dbtype']=='nodb'){ continue; }
            $kid = $fv['kid']; 
            $val = isset($r1[$kid]) ? $r1[$kid] : ""; 
            $fix = $kfix; // 默认后缀
            if(in_array($fv['type'],['select','cbox','radio'])){
                $arr = vopCell::optArray($fv['cfgs'], $val, 0);
                $val = implode(',', $arr);
            }elseif($fv['fmextra']=='datetm'){
                $val = self::dateParse($val, $fv['fmexstr']);
            }else{
                $fix = ''; // 无后缀
            }
            $row["$kid$fix"] = $val;
        }
        return $row;
    }
    static function dateParse($val, $fmt='Y-m-d'){ 
        $fmt = empty($fmt) ? 'Y-m-d' : $fmt;
        if(empty($val)){
            return '';
        }elseif(!is_numeric($val)){
            $val = strtotime($val);
        }
        return date($fmt, $val);
    }

    # ==========================================

    // 批量设置字段配置
    function setFcfg($cfgs, $mod){
        if($mod!=$cfgs['kid']){
            return "Error MOD: `$mod`!=`$cfgs[kid]`";
        }
        $tabid = 'base_fields';
        $def = ['model'=>$mod, 'enable'=>1, 'dbdef'=>''];
        foreach ($cfgs['list'] as $ck => $cv) {
            $cv['kid'] = $ck;
            $fhas = db()->table($tabid)->where("model='$mod' AND kid='$ck'")->find();
            if($fhas){
                db()->table($tabid)->data($cv)->where("model='$mod' AND kid='$ck'")->update();
            }else{
                db()->table($tabid)->data($cv+$def)->insert();
            }
            glbDBExt::setOneField($mod,$ck,'check');
        }
        glbCUpd::upd_model($mod);
        return 'OK!';
    }
    // 获取excel的字段配置
    static function xlsFcfg($fp, $rtb=0){
        //$pos = strpos($upres['url'], "/@udoc/");
        //$fp = DIR_DTMP.substr($upres['url'], $pos);
        $data = extExcel::exRead($fp, 'utf-8', $rtb); 

        $cells = empty($data['cells']) ? [] : $data['cells'];
        $res = [];
        foreach ($cells as $tk => $tv) {
            if(empty($tv[1]) && empty($tv[2]) && empty($tv[3])){
                continue;
            }elseif($tk==1){
                $res['kid'] = $tv[2];
                $res['title'] = $tv[7];
            }else{
                $fmexstr = $cfgs = '';
                $type = !empty($tv[4]) ? $tv[4] : 'input';
                if(!empty($tv[6]) && $type=='input'){
                    $type = 'select';
                }
                if(!empty($tv[6]) && preg_match("/\w+\.i/i", $tv[6])){
                    $cfgs = str_replace('.i', '', $tv[6]);
                }elseif(!empty($tv[6]) && strpos($tv[6],'、')){
                    // org、new; 原校区、新校区
                    $t1 = explode('、', $tv[6]);
                    $t2 = explode('、', $tv[8]);
                    foreach ($t1 as $k1 => $v1) {
                        $cfgs .= ($cfgs?"\n":'') . "$v1=".$t2[$k1];
                    }
                }elseif(!empty($tv[6]) && strpos($tv[6],'+') && strpos($tv[8],'、')){
                    // jg05+1; 五级、六级、七级、八级、九级、十级、十一级、十二级、十三级
                    $tab = explode('、', $tv[8]); 
                    $cres = self::fmtTarr($tab, $tv[6]);
                    $cfgs = $cres['cfg'];
                }
                $dbtype = $type=='parts' ? 'nodb' : 'varchar';
                $res['list'][$tv[2]] = [
                    'top' => $tv[1],
                    'etab' => $tv[3],
                    'type' => $type, // 4,6+8
                    'dblen' => $tv[5],
                    'dbtype' => $dbtype,
                    'title' => $tv[7],
                    'fmexstr' => $fmexstr,
                    'cfgs' => $cfgs,
                ];
                if(!empty($tv[8])){
                    if($tv[8]=='datetm'){ $res['list'][$tv[2]]['fmextra'] = $tv[8]; }
                }
            }
        }
        return $res;

    }

    // 批量设置类别项
    // $cfg = $this->fmtTarr(explode('、','业务部、技术部'), 'id012+4'); method($cfg['tab'], 'indep', '0')
    static function setTitems($tab, $mod, $pid='0'){
        $cfg = read($mod); $def = ['model'=>$mod, 'pid'=>$pid, 'enable'=>1];
        $tabid = empty($cfg['etab']) ? 'types_common' : "types_$mod";
        foreach ($tab as $tk => $tv) {
            $fhas = db()->table($tabid)->where("model='$mod' AND kid='$tk'")->find();
            $row = ['kid'=>$tk, 'title'=>$tv]; // kid   model   pid title   top
            if(empty($fhas)){
                db()->table($tabid)->data($row+$def)->insert();
            }else{
                db()->table($tabid)->data($row)->where("model='$mod' AND kid='$tk'")->update();
            }
        }
        glbCUpd::upd_model($mod);
        return 'OK!';
    }

    // 格式化数组/cfg
    // $arr = explode('、','八级、九级、十级、十一级'), $tpl = 'jg05+1';
    static function fmtTarr($arr, $tpl){ 
        preg_match("/(\d+)\+(\d+)/", $tpl, $p1); //dump($p1);
        $cfg = ''; $tab = [];
        foreach ($arr as $k2 => $v2) {
            $n2 = intval($p1[1]) + $k2*intval($p1[2]);
            $k2 = str_replace("$p1[1]+$p1[2]",'',$tpl) . substr("000000$n2",-1*strlen($p1[1]));
            $cfg .= ($cfg?"\n":'') . "$k2=$v2";
            $tab[$k2] = $v2;
        }
        return ['cfg'=>$cfg, 'tab'=>$tab];
    }

    static function getAbs($idno){
        $sex = substr($idno,16,1) % 2 == 1 ? 'm' : 'f'; //dump($sex);
        $birth = substr($idno,6,4).'-'.substr($idno,10,2).'-'.substr($idno,12,2); 
        $age = self::getAge($birth); 
        return ['sex'=>$sex, 'birth'=>$birth, 'age'=>$age]; 
    }
    static function getAge($birth='', $date=''){
        $date || $date = date('Y-m-d');
        $age = substr($date,0,4) - substr($birth,0,4); //dump($age);
        return (substr($date,5)<substr($birth,5)) ? $age-1 : $age;
    }

    // ==== rd-随机数据(start) ============================================

    // cfgs: type-p1-p2,(''),0,
    static function batRows($mod, $cnt=5, $cfgs=[]){ 
        $res = [];
        for($i=0;$i<$cnt;$i++){
            $res = self::r1Rows($mod, $cfgs);
        }
        return $res;
    }
    static function r1Rows($mod, $cfgs=[]){ 
        $fields = read("$mod.f");
        $items = read("$mod.i"); 
        $fmcfg = $excfg = []; $fmrow = $exrow = [];
        // fields
        foreach($fields as $fk=>$fv){
            if($fv['dbtype']=='nodb'){ continue; }
            if(!empty($fv['etab'])){
                $excfg[$fk] = $fv;
            }else{
                $fmcfg[$fk] = $fv;
            }
        }
        // data
        foreach(['fm','ex'] as $tk){
            $kcfg = "{$tk}cfg"; $krow = "{$tk}row"; 
            foreach($$kcfg as $fk=>$fv){
                $val = strstr($fv['dbtype'],'int') ? '0' : '';
                if(isset($cfgs[$fk])){ 
                    $tmp = explode('-',$cfgs[$fk].'---');
                    $mk = 'r1'.$tmp[0]; //echo "<br>$mk,";
                    if(method_exists('\imcat\dopSwap',$mk)){ //echo "(ok-$mk)";
                        $val = self::$mk($tmp[1],$tmp[2],$tmp[3]);
                    }
                }elseif(in_array($fv['type'],['radio','select','cbox'])){
                    $f1cfg = basElm::text2arr($fv); 
                    $val = array_rand($f1cfg,1);
                }
                $$krow[$fk] = $val; //echo "$val,";
            }
        }
        // catid,sex,birth,age
        if(!empty($items)){
            $f1cfg = basElm::text2arr($items); 
            $fmrow['catid'] = array_rand($f1cfg,1);
        } //dump($catid);
        // 
        $dbtab = glbDBExt::getTable($mod); $kar = glbDBExt::dbAutID($dbtab);
        $d0 = [substr($dbtab,0,1).'id'=>$kar[0], substr($dbtab,0,1).'no'=>$kar[1]]; 
        db()->table($dbtab)->data($d0+$fmrow)->insert();
        if(!empty($exrow)){
            $dbext = glbDBExt::getTable($mod, 1);
            db()->table($dbext)->data([substr($dbtab,0,1).'id'=>$kar[0]]+$exrow)->insert();
        }
        return [$fmrow, $exrow];
        //dump($exrow); dump($fmrow);
    }

    // 
    static function tabCity(){ 
        return ['北京','上海','广州','深圳','东莞','郴州','永州','怀化','杭州','长沙','西安','拉萨','乌鲁木齐','哈尔滨'];
    }
    static function r1Name(){ 
        $name1 = ['房','车','解','单','伍','庞','欧阳','司马','万','文','楚','左','闵','关','詹']; // 左,闵,关,詹
        $name2 = ['先生','女士','Peace','Jack','Robbin','Lisa','Lina','Rose','鸽子','花猫'];
        $r1 = mt_rand(0, count($name1)-1); $n1 = $name1[$r1];
        $r2 = mt_rand(0, count($name2)-1); $n2 = $name2[$r2];
        return $n1.$n2;
    }
    static function r1Area(){ 
        $res = '';
        $ac0 = self::tabCity();
        $res = $ac0[mt_rand(0,count($ac0)-1)].'市';
        return $res;
    }
    static function r1Addr(){ 
        $res = '';
        $ac0 = self::tabCity();
        $res .= $ac0[mt_rand(0,count($ac0)-1)].'市';
        $ar1 = ['中山','解放','环城','燕泉','东纵','振兴','新河','朝晖','建设','人民','和平','新华'];
        $ar2 = ['中','东','东','南','西','西','北','','',''];
        $res .= $ar1[mt_rand(0,count($ar1)-1)].$ar2[mt_rand(0,count($ar2)-1)].'路';
        $res .= mt_rand(58,889).'号';
        return $res;
    }
    static function r1Corp(){ 
        $res = ''; // 广东卓科电子 ,科技有限公司, 有限责任公司,股份有限公司,集团有限公司
        $ac0 = self::tabCity();
        $res .= $ac0[mt_rand(0,count($ac0)-1)].(mt_rand(1,100)>50?'市':'');
        $ar1 = ['卓','越','华','夏','蓝','图']; $t1 = $ar1[mt_rand(0,count($ar1)-1)];
        $res .= mt_rand(1,100)>50 ? "`{$t1}X某`" : "`某X{$t1}`";
        $ar2 = ['科技有限','技术有限','有限责任','股份有限','集团有限'];
        $res .= $ar2[mt_rand(0,count($ar2)-1)].'公司';
        return $res;
    }
    static function r1Mob(){ // 135
        $a1 = ['13','15','17','18'];
        return $a1[mt_rand(0,count($a1)-1)].basKeyid::kidRand('0',9);
    }
    static function r1Idno($date=''){ // date-由日期生成
        $res = ''; // 432823-19790722-4513
        $res .= mt_rand(1,8).mt_rand(11111,99999);
        if(empty($data)){
            $res .= self::r1Birth();
        }elseif(is_numeric($data)){
            $res .= strlen("$date")==8 ? $date : date('Ymd');
        }else{ //
            $res .= str_replace(['-','.','/'],'',$date);
        }
        $res .= mt_rand(100,999);
        $res .= basStr::isIdCard($res, 1);
        return $res;
    }
    static function r1Birth($y1=3, $y2=60, $re='num'){ // 3-60岁
        $y4 = date('Y') - mt_rand($y1,$y2); //1-60岁
        $m2 = mt_rand(1,12); if($m2<10){ $m2 = "0$m2"; }
        $d2 = mt_rand(10,30);
        $res = $re=='num' ? $y4.$m2.$d2 : "$y4-$m2-$d2";
        return $res;
    }
    static function r1Num($min, $max){ // max='bit'-生成min位数字
        if($max=='bit'){
            $res = basKeyid::kidRand('0',$min);
        }else{
            $res = mt_rand($min, $ma);
        }
        return $res;
    }

    // ==== rd-随机数据(end) ============================================

}

/*

* 批量设置类别
    $str = '管理部、业务部、技术部';
    $tab = explode('、', $str);
    $cfg = dopSwap::fmtTarr($tab, 'id012+4');
    $res = dopSwap::setTitems($cfg['tab'], 'indep', '0');
* 批量填演示数据
    $rcfg = ['title'=>'Name', 'mtel'=>'Mob', 'idno'=>'Idno', 'dutime'=>'Num-0-8', 'jiguan'=>'Area', 
        ' hukou'=>'Addr', 'addr'=>'Addr', 'mrname'=>'Name', 'mrcorp'=>'Corp', ];
    #dopSwap::batRows('hrdoc', 3, $rcfg);
* 输出数据、字段配置到Excel
    $res = dopSwap::outMdemo('hrdoc', '', 5);
* Excel字段配置 -> 到数据库
    $fp = DIR_VIEWS.'/comm/dimp/Data-Table.xls';
    $data = dopSwap::xlsFcfg($fp); dump($data); // 获取excel的字段配置
    $res = dopSwap::setFcfg($data, 'hrdoc'); // 批量设置字段配置
* Excel数据 -> 保存db
    $fp = DIR_VIEWS.'/umc/hrdoc/data/Demo-Imp10.xls';
    $unk = ['idno']; // 唯一数据行条件
    $must = ['title','idno']; // 为空忽略
    $res = dopSwap::xlsDimp($fp, 'hrdoc', $unk, $must); 

*/
