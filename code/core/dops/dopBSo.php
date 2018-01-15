<?php

// dopBSo : 基本数据操作(data OP(base) for Search) --- 搜索...
// for : dopSo,
class dopBSo{    

    public $cfg = array();
    public $mod = '';
    public $typfid = 'catalog';
    public $order = '';
    public $dskey = ''; //默认搜索字段
    public $urlstr = '';
    public $whrstr = '';

    //function __destory(){  }
    function __construct($cfg,$tabid){ 
        $this->cfg = $cfg; 
        $this->mod = empty($cfg['kid']) ? 0 : $cfg['kid'];
        $this->tbid = $tabid;
        $this->type = $cfg['pid']; 
    }
    // 搜索项-类别
    function Type($w,$msg='(null)'){ 
        $stype = basReq::val('stype','','Key');
        if($stype){
            $this->urlstr .= "&stype=$stype";
            if(in_array($this->type,array('docs','advs'))){ 
                $this->whrstr .= basSql::whrTree($this->cfg['i'],$this->typfid,$stype);
            }elseif(in_array($this->type,array('users','xxxxx'))){
                $this->whrstr .= " AND $this->typfid='$stype'";
            }elseif(in_array($this->type,array('coms','xxxxx'))){
                $this->whrstr .= " AND $this->typfid='$stype'";
            }
        }
        if(in_array($this->type,array('coms','xxxxx'))){
            $str = "\n<input name='stype' type='text' class='w90' value='$stype'>";
        }else{
            $str = "\n<select name='stype' class='w$w'>"; 
            if($msg=='(null)') $msg=basLang::show('flow.op0_type');
            $str .= comTypes::getOpt($this->cfg['i'],$stype,$msg,0); 
            $str .= "</select>";
        } 
        return $str;
    }
    // 搜索项-Keyword
    function Word($w1=80,$w2=90,$msg='(null)',$soarr=array()){ 
        if($msg=='(null)') $msg = basLang::show('flow.op0_filt');
        $sfid = basReq::val('sfid',$this->dskey,'Key');
        $sfop = basReq::val('sfop','lb','Key');
        $sfkw = basReq::val('sfkw');
        if($sfkw && isset($this->cfg['f'][$sfid])){ 
            $this->urlstr .= "&sfid=$sfid&sfkw=$sfkw&sfop=$sfop";
            $fcfg = $this->cfg['f'][$sfid];
            // fmextra=winpop
            if(!empty($fcfg['type']) && in_array($fcfg['type'],array('select','cbox','radio'))){
                $this->whrstr .= basSql::whrScbr($this->cfg['f'],$sfid,$sfkw); //" AND `$sfid` LIKE '$sfkw%'";
            }else{
                if($sfop=='ll') $this->whrstr .= " AND `$sfid` LIKE '$sfkw%'";
                if($sfop=='lb') $this->whrstr .= " AND `$sfid` LIKE '%$sfkw%'";    
                if($sfop=='lr') $this->whrstr .= " AND `$sfid` LIKE '%$sfkw'";
                if($sfop=='eq') $this->whrstr .= " AND `$sfid`='$sfkw'";
            }
        } 
        $str  = "\n<select name='sfid' class='w$w1'>"; 
        $str .= $this->Options('char',$sfid,$soarr); 
        $str .= "</select>";
        $str .= "<select name='sfop' class='w$w2'>";
        $str .= basElm::setOption($this->sopc,$sfop,$msg);  
        $str .= "</select>";
        $str .= "<input name='sfkw' type='text' class='w90' value='$sfkw'>";
        return $str;
    }
    // 搜索项-范围
    function Area($w=90,$w2=90,$rfield=''){ 
        $sfrng = $rfield ? $rfield : basReq::val('sfrng','','Key');
        $srva = basReq::val('srva');
        $srvb = basReq::val('srvb');  
        if((!empty($srva) || !empty($srvb)) && (isset($this->cfg['f'][$sfrng]) || isset($this->fext[$sfrng]))){
            $this->urlstr .= "&sfrng=$sfrng&srva=$srva&srvb=$srvb";
            empty($srva) || $this->whrstr .= basSql::whrDate($sfrng,$srva,'>',$this->cfg);
            empty($srvb) || $this->whrstr .= basSql::whrDate($sfrng,$srvb,'<',$this->cfg);
        }
        if(empty($rfield)){
            $str = "\n<select name='sfrng' class='w$w'>";
            $str .= $this->Options('num',$sfrng); 
            $str .= "</select>";
        }else{
            $str = "\n<input name='sfrng' type='hidden' value='$rfield'>";
        }    
        $str .= "<input name='srva' type='text' class='w$w2' value='$srva'>~";
        $str .= "<input name='srvb' type='text' class='w$w2' value='$srvb'>";
        return $str;
    }
    // 搜索项-字段(select,winpop)
    function Field($key,$w=90){ 
        $_groups = glbConfig::read('groups');
        $val = basReq::val($key,'','Key');
        $fc = @$this->cfg['f'][$key];
        $ftype = @$fc['type']; $cfgs = @$fc['cfgs'];
        $extra = @$fc['fmextra']; $exstr = @$fc['fmexstr'];
        $opt = $str = '';    
        if($extra=='winpop' && isset($_groups[$exstr])){
            $opt = comTypes::getOpt($exstr,@$val,'-'.$fc['title'].'-','',0);    
        }elseif($ftype=='select' && isset($_groups[$cfgs])){
            $opt = comTypes::getOpt($cfgs,@$val,'-'.$fc['title'].'-','',0);    
        }elseif($ftype=='select' && $cfgs){
            $opt = basElm::setOption($cfgs,@$val,'-'.$fc['title'].'-','',0); 
        }elseif($ftype=='cbox' && $cfgs){
            $opt = basElm::setOption($cfgs,@$val,'-'.$fc['title'].'-','',0); 
        } 
        if($opt){
            $str = "\n<select name='$key' class='w$w'>$opt</select>";
        }
        if($val && isset($this->cfg['f'][$key])){
            $this->urlstr .= "&$key=$val";
            if($extra=='winpop' && isset($_groups[$exstr])){ //可能多选...
                $size = explode('x',$fc['fmsize'].'x');
                $_n = empty($size[1]) ? 1 : intval($size[1]);
                if($_n>1){
                    $this->whrstr .= " AND $key LIKE '%$val%'";
                }else{
                    $imod = glbConfig::read($exstr); 
                    $this->whrstr .= basSql::whrTree($imod['i'],$key,$val);
                }
            }elseif($ftype=='select' && isset($_groups[$cfgs])){
                $imod = glbConfig::read($cfgs); 
                $this->whrstr .= basSql::whrTree($imod['i'],$key,$val);    
            }elseif($ftype=='select' && $cfgs){
                $this->whrstr .= " AND $key='$val'";    
            }elseif($ftype=='cbox' && $cfgs){
                $this->whrstr .= " AND $key LIKE '%$val%'";
            }  
        } 
        return $str;
    }
    // 搜索项-Show()
    function Show($w=70){ 
        $item = basElm::setOption("s1=".basLang::show('flow.op_show')."\ns0=".basLang::show('flow.op_hide')."",@$val,basLang::show('flow.op0_show')); 
        $str = "\n<select name='show' class='w$w'>$item</select>";
        return $str;
    }
    // 搜索项-排序
    function Order($ord_now,$w=80,$msg='(null)',$opubs='-1'){ 
        if($msg=='(null)') $msg = basLang::show('flow.op0_order');
        $ord_pub = $opubs==='-1' ? array('atime' => basLang::show('flow.log_atime'),'etime' => basLang::show('flow.log_etime')) : $opubs;
        $str = "\n<select name='order' class='w$w'>"; 
        $ords = array_merge($ord_pub,$ord_now);
        $str .= basElm::setOption($ords,$this->order,$msg); 
        $str .= "</select>";
        return $str;
    }
    // 搜索项-form
    function Form($bar,$msg,$w,$khid=array()){
        global $_cbase; 
        $run = $_cbase['run'];
        $mod = $this->mod;
        $bar .= "\n&nbsp; <input name='sch_$mod' class='btn' type='submit' value='".basLang::show('flow.dops_search')."'>";
        echo "\n<form id='fmid' name='fmid' method='GET' action='?".$this->urlstr."'>";
        empty($run['sobarnav']) || $bar = $run['sobarnav']."$bar";
        glbHtml::tab_bar($msg,$bar,$w,'tl');
        echo "\n<input name='mkv' type='hidden' value='".basReq::val('mkv')."' />";
        echo "\n<input name='mod' type='hidden' value='$mod' />";
        echo "\n<input name='view' type='hidden' value='".basReq::val('view')."' />";
        echo "\n<input name='pid' type='hidden' value='".basReq::val('pid')."' />";
        echo "\n<input name='did' type='hidden' value='".basReq::val('did')."' />";
        echo "\n<input name='part' type='hidden' value='".basReq::val('part')."' />";
        echo "\n<input name='act' type='hidden' value='".basReq::val('act')."' />";
        foreach($khid as $k=>$v){
            echo "\n<input name='$k' type='hidden' value='$v' />";
        }
        echo "\n</form>";
        //return $str;
    }

    // getOptions(Select)
    function Options($types='',$def='',$soarr=array()){ 
        if($types=='char'){
            $msg = basLang::show('flow.op0_type');
            $ft = 'varchar';
        }elseif($types=='num'){
            $msg = basLang::show('flow.op0_area');
            $ft = 'tinyint,int,float';
        }else{
            $msg = basLang::show('flow.op0_search');
            $ft = '';    
        }
        if(!empty($soarr)){
            $arr = array();
            $soarr;
            foreach($soarr as $k=>$v){
                if(is_numeric($k))    {
                    $arr[$v] = $v;
                }else{
                    $arr[$k] = $v;    
                }
            } 
        }else{
            $arr = basArray::opaFields($this->cfg['f']+$this->fext,$ft);
        }
        return basElm::setOption($arr,$def,$msg);
    }

}
