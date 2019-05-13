<?php
namespace imcat;

// dopBCv : 基本数据操作(data OP(base) for CellView) --- 单元格显示...
// for : dopBCv,
class dopBCv{    

    public $cfg = array();
    public $mod = '';
    public $typfid = 'catalog';
    public $order = '';
    public $dskey = '';
    public $urlstr = '';
    public $whrstr = '';

    //function __destory(){  }
    function __construct($cfg,$tabid){ 
        $this->cfg = $cfg;
        $this->mod = empty($cfg['kid']) ? 0 : $cfg['kid'];
        $this->tbid = $tabid;
        $this->type = $cfg['pid']; 
    }
    
    function PTitle($mod,$val,$url='',$len=32,$filed=''){ //,$kid='',$link=''
        $len = is_numeric($len) ? $len : 32;
        if($mod=='users'){
            $uinfo = usrBase::uget_minfo($val);    
            $re = @$uinfo['uname'];
            if(isset($uinfo['company'])) $re = $uinfo['company'];
        }else{
            $re = dopFunc::vgetTitle($mod,$val,$filed);
        }
        $re = basStr::filTitle($re);
        $re = empty($re) ? "<span class='cCCC'>---</span>" : $re;
        $re = basStr::cutWidth($re, $len);
        $re = dopFunc::vgetLink($re,$mod,'',$url); 
        echo "<td>".$re."</td>\n"; // class='tc'
    }
    // 显示项-Select
    function Select($val,$td=1){
        $val = "<input name='fs[$val]' type='checkbox' class='rdcb' value='1' />";
        if(empty($td)) return $val;
        return "<td class='tc'>$val</td>\n";
    }
    // 显示项-Title
    function Title($r,$td=1,$key='title',$url='',$len=32,$exstr=''){ 
        $val = $r[$key]; 
        $val = basStr::cutWidth($val,$len);
        if(!empty($r['color'])) $val = "<span style='color:#{$r['color']}'>$val</span>"; 
        $_key = substr($this->type,0,1).'id'; 
        $val = dopFunc::vgetLink($val,$this->mod,$r[$_key],$url); 
        if(!empty($r['mpic'])){ 
            $ticon = comFiles::getTIcon($r['mpic']);
            $val = "<span class='c33F'>".($ticon['icon']=='pic' ? basLang::show('core.bcv_pic') : basLang::show('core.bcv_file'))."</span>$val"; 
        }
        if(empty($td)) return $val;
        return "<td class='tl'>$val{$exstr}</td>\n";
    }
    // 显示项-TKeys, winpop,select,cbox,
    function TKeys($r,$td=1,$key,$len=12,$null='',$color=1){
        $val = $r[$key]; $vbak = $val; $vre = array();
        $fc = @$this->cfg['f'][$key]; //if($key=='areas') dump($fc);
        $vre = vopCell::optArray($fc,$val,$color);
        if(empty($vre)){
            $val = empty($vbak) ? $null : $vbak;
        }else{
            $val = implode(',',$vre);
        }
        if(empty($td)) return $val;
        return "<td class='tc'>$val</td>\n";     
    }
    // 显示项-Types
    function Types($val,$td=1){
        $val = empty($this->cfg['i'][$val]['title']) ? $val : $this->cfg['i'][$val]['title'];
        if(empty($td)) return $val;
        return "<td class='tc'>$val</td>\n"; 
    }
    // 显示项-Show
    function Show($val,$td=1){
        $val = glbHtml::null_cell($val);
        if(empty($td)) return $val;
        return "<td class='tc'>$val</td>\n";
    }
    // 显示项-Time
    function Time($val,$td=1,$fmt='',$end=0){
        global $_cbase;
        if(empty($fmt)) $fmt='Y-m-d H:i';
        if($fmt=='y') $fmt='y-m-d H:i';
        if($fmt=='D') $fmt='Y-m-d';
        if($fmt=='d') $fmt='y-m-d';
        $val = empty($val) ? "<span class='cCCC'>---</span>" : date($fmt,$val);
        if(!empty($end)){
            $vc = date('Y-m-d',$_cbase['run']['stamp']);
            $vd = substr($val,0,10); 
            if($vc===$vd){
                $val = "<span class='c00F'>$val</span>";
            }elseif($vc>$vd){
                $val = "<span class='cF00'>$val</span>";
            }
        }
        if(empty($td)) return $val;
        return "<td class='tc'>$val</td>\n"; 
    }
    // 显示项-Field
    function Field($val,$td=1,$len=6,$url=''){ 
        $val = basStr::cutWidth($val,$len,'..');
        $val = basStr::filTitle($val);
        $val = empty($val) ? "<span class='cCCC'>---</span>" : $val;
        if(!empty($url)) $val = "<a href='$url' target='_blank'>$val</a>";
        if(empty($td)) return $val;  
        return "<td class='tc'>$val</td>\n";
    }
    // 显示项-Url
    function Url($title,$td=1,$url,$twin='',$w=780,$h=560){ 
        if($twin=='frame'){ 
            $url .= "&frame=1";
            $twin = " target='_blank'";
        }elseif($twin=='blank'){
            $twin = " target='_blank'";
        }elseif(!empty($twin)){
            $twin = " onclick='return winOpen(this,\"$twin\",$w,$h);'";
        }else{
            $twin = '';
        }
        $val = "<a href='$url'$twin>$title</a>";
        if(empty($td)) return $val; 
        return "<td class='tc'>$val</td>\n";
    }
    
    // 显示项-set_opts
    function set_opts($key){ 
        //set_new|新建\nset_doing|处理中\nset_paid|已付款\nset_send|已发货\nset_return|退货\n
        //$val = $r[$key]; $vbak = $val; $vre = array();
        $fc = @$this->cfg['f'][$key];
        $ftype = @$fc['type']; $cfgs = @$fc['cfgs'];
        $arr = array();
        $extra = @$fc['fmextra']; $exstr = @$fc['fmexstr'];
        $arr = array();
        if($extra=='winpop' && isset($_groups[$exstr])){ 
            $arr = basElm::text2arr($exstr);
        }elseif(in_array($ftype,array('select','cbox'))){
            $arr = basElm::text2arr($cfgs);
        }
        //$va = explode(',',$val); 
        $re = "";
        foreach($arr as $k=>$v){
            $re .= "\nset_$k|".basLang::show('core.bcv_set')."`$k`$v";
        }
        return $re; 
    }

}

