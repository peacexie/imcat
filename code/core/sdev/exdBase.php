<?php

// ...类exdBase
class exdBase{    
    
    public $mod = '';
    public $mcfg = '';
    public $mpid = '';
    public $mkid = '';
    public $mkno = '';
    
    public $db = NULL;
    public $tbid = '';
    public $tbext = '';
    
    public $fmv = array(); // fields中：表单数据(基本表)
    public $fmu = array(); // fields中：表单数据(扩展表)
    public $fme = array(); // fields外数据：(ip,time,user,catid,grade...)
    
    //function __destory(){  }
    function __construct($mod){ 
        $this->db = db();    
        $this->minit($mod);
    }
    
    function minit($mod=''){
        $this->mod = $mod;
        $this->mcfg = read($this->mod); 
        $this->mpid = @$this->mcfg['pid']; 
        $_tmp = array(
            'docs' =>array('dopDocs','did'),
            'users'=>array('dopUser','uid'),
            'coms' =>array('dopComs','cid'),
            'advs' =>array('dopAdvs','aid'),
        ); 
        if(!isset($_tmp[$this->mpid])) glbHtml::end(lang('flow.dops_parerr').':mod@dop.php');
        $this->mkid = $_tmp[$this->mpid][1]; 
        $this->mkno = substr($this->mkid,0,1).'no'; 
        $_cls = $_tmp[$this->mpid][0]; 
        $this->tbid = "{$this->mpid}_$this->mod";
        $this->tbext = $this->mpid =='users' ? 'users_uacc' : "dext_$this->mod";
    }

    // OutputData // mod,stype,limit(1-500),order(did:ASC),offset
    function odata($cfg=array(),$exJoin=1,$whrsub=''){
        $stype = (!empty($cfg['stype'])) ? $cfg['stype'] : req('stype'); $whrstr = '';
        if($stype && in_array($this->mpid,array('docs','advs'))){ 
            $whrstr .= basSql::whrTree($this->mcfg['i'],'catid',$stype);
        }elseif($stype && in_array($this->mpid,array('users'))){
            $whrstr .= " AND grade='$stype'";
        }
        $limit = (!empty($cfg['limit'])) ? $cfg['limit'] : req('limit',10,'N');
        if($limit<1) $limit = 10;
        $order = (!empty($cfg['order'])) ? $cfg['order'] : explode(':',req('order').":");
        $okey = ($order[0]==$this->mkid || isset($this->mcfg['f'][$order[0]])) ? $order[0] : $this->mkid;
        $omod = in_array(strtoupper($order[1]),array('ASC','DESC','EQ')) ? strtoupper($order[1]) : 'DESC';
        $offset = (!empty($cfg['offset'])) ? $cfg['offset'] : req('offset');
        if($offset){
            if(strstr($omod,'DESC')){
                $op = '<';
            }elseif(strstr($omod,'ASC')){
                $op = '>';
            }else{ // EQ
                $op = '=';
            }
            $whrstr .= " AND {$this->mkid}$op'$offset'";
        }
        $whrstr = $whrstr ? substr($whrstr,5) : '1=1'; 
        $data = $this->db->table($this->tbid)->where($whrstr.$whrsub)->order("$okey $omod")->limit($limit)->select(); 
        if($this->mpid=='docs' && $exJoin) dopFunc::joinDext($data,$this->mod,'did'); //$this->odataExt($data);
        return $data;
    }
    
    function svMerge(&$data){
        $fskip = basElm::line2arr($this->jcfg['fskip']); //排除字段
        if(!empty($fskip)){
            foreach($fskip as $fk){
                unset($data[$fk]);
            }
        }
        $def = basElm::text2arr($this->jcfg['fdefs']); //默认值
        if(!empty($def)) $data = array_merge($data,$def);
    }
    
    function svFields($data){ 
        $this->svMerge($data); 
        $f = $this->mcfg['f'];
        foreach($f as $k=>$v){ 
            if(!isset($data[$k])){
                if(strstr($v['dbtype'],'char')) $data[$k] = '';
                if(strstr($v['dbtype'],'int')) $data[$k] = 0;
                if(in_array($v['dbtype'],array('float','double','decimal'))) $data[$k] = 0; 
            }
        }
        foreach($data as $k=>$v){ 
            if(isset($f[$k])){ 
                if($f[$k]['dbtype']=='nodb') continue;
                $val = dopFunc::svFmtval($f,$this->mod,$k,$v);
                if($this->mcfg['pid']=='docs' && !empty($f[$k]['etab'])){ 
                    $this->fmu[$k] = $val; // (扩展表)
                }else{ 
                    $this->fmv[$k] = $val; // (基本表)
                }
            }else{
                if(in_array($k,array($this->mkid,$this->mkno,'aip','atime','auser','eip','etime','euser'))){
                    $v = $k==$this->mkno ? 0 : $v;
                    $this->fmv[$k] = $v;
                }elseif(in_array($k,array('grade')) && $this->mcfg['pid']=='users'){
                    $this->fmv[$k] = $v;
                }elseif(in_array($k,array('catid')) && $this->mcfg['pid']=='docs'){
                    $this->fmv[$k] = $v; 
                }else{ // (xid,xno,aip,atime,auser,catid,grade...)
                    $this->fme[$k] = $v; // fields外数据：
                }
            }  
        } //print_r($this->fmv); print_r($this->fmu); //print_r($this->fme);
    }
    function svAccount(){ 
        $fma['uid'] = $this->fmv['uid']; $uname = str_replace('-','',$this->fmv['uid']);
        $fma['uno'] = $this->fmv['uno']; 
        $fma['uname'] = isset($this->fmv['uname']) ? $this->fmv['uname'] : addUname($uname,$this->mod); 
        $fma['umods'] = $this->mod; 
        $fma['upass'] = comConvert::sysPass($fma['uname'],basKeyid::kidRand('24',12),$this->mod);
        $fma['aip'] = '('.req('act').':'.req('job').')';
        $this->db->table($this->tbext)->data(basReq::in($fma))->insert();
    }
    function svDocsext(){ 
        $this->fmu['did'] = $this->fmv['did']; 
        $this->db->table($this->tbext)->data(basReq::in($this->fmu))->insert(0);
    }
    // save
    function save($data){
        $this->svFields($data,$this->jcfg);
        if($this->mcfg['pid']=='docs'){
            $this->svDocsext();
        }elseif($this->mcfg['pid']=='users'){
            $this->svAccount();    
        }
        $this->db->table($this->tbid)->data(basReq::in($this->fmv))->insert();
        return 1;
    }
    // supd
    function supd($data){
        $this->svFields($data,$this->jcfg);
        $kname = $this->mkid;
        $kval = $this->fmv[$this->mkid];
        if($this->mcfg['pid']=='docs'){
            $this->db->table($this->tbext)->data(basReq::in($this->fmu))->where("did='$kval'")->update();
        }
        unset($this->fmv[$this->mkid]);
        $this->db->table($this->tbid)->data(basReq::in($this->fmv))->where("$kname='$kval'")->update(); 
        return 1;
    }
    
    // getKid
    function getJKid($kid=''){
        $tabid = $this->mpid=='users' ? $this->tbext : $this->tbid;
        $kar = glbDBExt::dbAutID($tabid,'yyyy-md-','32',$kid);
        return $kar;
    }
    
    // getJFm2(max|min($field))
    function getJFm2($tab,$job,$field,$func){ 
        $tab = $this->db->table($tab,2);
        $sql = "SELECT $func($field) AS $field FROM $tab WHERE kid='$job'"; 
        $rec = $this->db->query($sql);
        $res = empty($rec[0][$field]) ? '' : $rec[0][$field];
        return $res;
    }

    // 获取配置字段
    function getJFlds($job){
        if(empty($this->cfields)){ 
            $cfields = $this->db->table('exd_sfield')->where("model='$job'")->select();
            $farr = array();
            foreach($cfields as $k=>$v){ 
                foreach($v as $k2=>$v2){ 
                    if(in_array($k2,array('model','aip','atime','auser','eip','etime','euser'))) unset($v[$k2]);
                }
                $farr[$v['kid']] = $v;    
            } //print_r($farr);
            $this->cfields = $farr; 
        } 
        return $this->cfields;
    }
        
    // getSign
    // $key : 单独设置(暂未使用...)
    static function getJSign($key=''){
        $ocfgs = read('outdb','ex');
        $safix = cfg('safe.safix'); 
        $sign = $ocfgs['sign']; // (empty($key)||empty($ocfgs["sign_$key"])) ? $ocfgs['sign'] : $ocfgs["sign_$key"];
        $usign = "{$safix}[sapp]={$sign['sapp']}&{$safix}[skey]={$sign['skey']}";
        return $usign;
    }
    
    // getCfg
    static function getJCfgs($act,$job){
        $jcfg = db()->table("exd_$act")->where("kid='$job'")->find(); 
        return $jcfg;
    }

    // fldForm
    static function fldForm($fm,$n=5){
        $stxmao = cfg('server.txmao');
        $marr = basLang::ucfg('cfglibs.exdbase_mode');
        $mext = basLang::ucfg('cfglibs.exdbase_ext');
        for($i=1;$i<=$n;$i++){ $k = "orgtg$i"; 
            $a = explode('(:)',$fm[$k].'(:)(:)');
            $mopt = "<br><select name='fm[$k][mode]' class='w150'>".basElm::setOption($marr,$a[0],lang('exdb_mode'))."</select>";
            $mopt .= " &nbsp; <select name='fm[$k][ext]' class='w150'>".basElm::setOption($mext,$a[2],lang('exdb_exop'))."</select>";
            $mopt .= " &nbsp; <a href='{$stxmao}/dev.php?advset-exdata#s_fields' target='_blank'>".lang('exdb_rnote')."</a>";
            glbHtml::fmae_row(lang('exdb_orgtag')."$i","<textarea name='fm[$k][tag]' rows='2' cols='50' wrap='wrap'>{$a[1]}</textarea>$mopt");
        }
    }
    // fldSave
    static function fldSave(&$fm,$n=5){
        for($i=1;$i<=$n;$i++){ $k = "orgtg$i"; 
            $mode = $fm[$k]['mode'];
            $tag = $fm[$k]['tag'];
            $ext = $fm[$k]['ext'];
            $fm[$k] = $fm[$k]['mode'].'(:)'.$fm[$k]['tag'].'(:)'.$fm[$k]['ext'];
        }
    }
    
    // showRes
    static function showRes($res){
        $msg = lang('exdb_nres');
        $msg .= $res['msg'] ? $res['msg']."<br>" : lang('exdb_okn',"{$res['ok']}/{$res['cnt']}")."<br>\n";
        $msg .= "[".date('Y-m-d H:i:s')."]<br>\n";
        if($res['next']){
            $msg .= "<br>\n".lang('exdb_next',3)."<br>\n";
            $js = "setTimeout('window.location.reload();',3000);"; 
            $js = basJscss::jscode($js);
        }else{
            $msg .= "<br>\n".lang('exdb_rok')."<br>\n";
            $js = '';    
        }
        glbHtml::page(lang('exdb_bugres'));
        glbHtml::page('body');
        echo "\n<p>$msg</p>\n$js";
        basDebug::varShow($res);
        glbHtml::page('end');
    }
    // showBug
    static function showBug($res,$exd,$debug){
        glbHtml::page(lang('exdb_bugres'));
        @$cfield = $exd->cfields[req('field')]; 
        if(strstr($cfield['dealfmts'],'strtotime')){
            $res .= " \n(".lang('exdb_orgdata').":".@$_cbase['crawl']['strtotime'].")";
        }elseif(strstr($cfield['dealfmts'],'strtotime')){
            $res .= " \n(".lang('exdb_orgdata').":".@$_cbase['crawl']['dealfunc'].")";    
        }
        echo "\n<base target='_blank' />";
        glbHtml::page('body');
        if($debug=='links'){
            foreach($res as $url){
                echo "\n<li><a href='$url'>$url</a></li>";    
            }
        }else{
            $str = is_array($res) ? 'Array' : $res;
            echo "\n<textarea rows='15' wrap='wrap' style='width:100%;height:50%'>".basStr::filForm($str)."</textarea>";    
        }
        echo "\n<hr>\n"; 
        basDebug::varShow($res);
        glbHtml::page('end');
    }
    
    // xxxrepVal
    static function xxxrepVal($val){
        //$val = '';
        return $val;
    }

}



