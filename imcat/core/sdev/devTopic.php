<?php
namespace imcat;

// ... 专题类

class devTopic{    

    static function fmtList($did,$part,$utag=1,$upic=0){
        $whr = "did='$did' AND part='$part'";
        $list = db()->table('topic_items')->where($whr)->order('top,did')->select();
        foreach ($list as $k => $row) {
            $list[$k] = self::fmtRow($row,0,$utag,$upic);
        }
        return $list;
    }
    static function fmtRow($did,$dno,$utag=1,$upic=0){
        is_string($did) && $whr = "did='$did' AND dno='$dno'";
        $row = is_array($did) ? $did : db()->table('topic_items')->where($whr)->find();
        if($utag && !empty($row['tags'])){
            $arr = json_decode(@$row['tags'],1);
            if(!empty($arr)) $row['utags'] = $arr;
        }
        if($upic && !empty($row['detail'])){
            if($upic=='crels'){
                $row['urels'] = @json_decode($data['detail'],1); 
            }else{
                $tmp = comStore::revSaveDir($row['detail']); 
                $row['upics'] = basElm::line2arr($tmp,0,";");
            }
        } //print_r($row);
        return $row;
    }
    static function fmtCfgs($data){
        include(DIR_IMCAT.'/lang/pubs/fcfgs.php');
        $cfgs = array();
        foreach($fcfgs as $key=>$cfg){ 
            if(!empty($data[$key])){
                $cfgs[$key]['title'] = $cfg['title'];
                $cfgs[$key]['cfgo'] = $data[$key];
                $cfgs[$key]['cfgs'] = devTopic::cfg2arr($data[$key]);
            } 
        } //print_r($cfgs);
        return $cfgs;
    }

    // --------------------------------------------------------- 

    // nav
    static function navBar($fcfgs,$xfm,$view='',$part=''){
        global $_cbase;
        extract(basReq::sysVars());
        $mkv = $_cbase['mkv']['mkv'];
        $burl = "?$mkv&mod=$mod&did=$did&view";
        $icls = 'class="col-xs-4 col-sm-3 col-md-2 col-lg-1 tc pa3"';
        $nav = "\n"; //print_r($xfm);
        if(!empty($did)){
            $nav .= "<ul class='clear'><li $icls><b>配置&相关</b></li>\n";
            $act = $view=='cfgs' ? 'cF00' : '';
            $nav .= "<li $icls><a href='$burl=cfgs' class='$act'>区块配置</a></li>\n"; 
            if(!empty($xfm['crels'])){
                $act = $view=='crels' ? 'cF00' : '';
                $nav .= "<li $icls><a href='$burl=crels' class='$act'>相关信息</a></li>\n";
            }
            if(!empty($xfm['cform'])){
                $act = $view=='cform' ? 'cF00' : '';
                $nav .= "<li $icls><a href='$burl=cform' class='$act'>表单项</a></li>\n";
                $act = $view=='formd' ? 'cF00' : '';
                $nav .= "<li $icls><a href='$burl=formd' class='$act'>表单数据</a></li>\n";
            }
            $nav .= "</ul>\n<ul class='clear'><li $icls><b>图文&媒体</b></li>\n";
            foreach (array('ctext','chtml','cpics','cmedia') as $key) {
                if(!empty($xfm[$key])){ $itm = $fcfgs[$key];
                    $act = $view==$key ? 'cF00' : '';
                    $nav .= "<li $icls><a href='$burl=$key' class='$act'>{$itm['title']}</a></li>\n";
                }
            }
            $nav .= "</ul>\n<ul class='clear'><li $icls><b>列表&投票</b></li>\n";
            foreach (array('clist','cvote') as $key) {
                if(!empty($xfm[$key])){
                    $cfgs = self::cfg2arr($xfm[$key]);
                    foreach ($cfgs as $k2=>$title) {
                        if(devTopic::skip($k2)) continue;
                        $act = $part==$k2 ? 'cF00' : '';
                        $nav .= "<li $icls><a href='$burl=$key&part=$k2' class='$act'>$title</a></li>\n";
                    }
                }
            }
            $nav .= "</ul>\n";
        }
        $nav .= "<div class='clear block h01 ma10' style='border-top:1px solid #CCC;' />&nbsp;</div>\n";
        return $nav;
    }

    // xxx
    static function cfg2arr($cfg){
        $cfgs = basElm::line2arr($cfg);
        $res = array();
        foreach ($cfgs as $row) {
            $pos = strpos($row,'=');
            $res[substr($row,0,$pos)] = substr($row,$pos+1);

        } //print_r($cfgs); print_r($res);
        return $res;
    }

    // tplLists=模板列表
    static function tplLists(){
        global $_cbase;
        $root = DIR_VIEWS.$_cbase['topic']['tpldir'];
        $list = comFiles::listScan($root,'',array('_index'));
        $list = array_keys($list);
        return $list;
    }

    // fmlist=
    static function fmlist($fmv,$fme,$view,$part){
        $nfs = read('news.f'); 
        $cfgs = self::cfg2arr($fme[$view]); //print_r($cfgs);
        // fields
        $ufs = array('title','mpic','jump','detail');
        if(in_array($view,array('cpics','chtml'))) unset($ufs[1]);
        foreach ($ufs as $fk) {
            $v = $nfs[$fk];
            if($fk=='detail' && $view!='chtml'){
                $v['fmextra'] = ''; 
                $v['fmsize'] = '640x12';
            }
            if($fk=='detail' && $view=='cpics'){
                $v['fmextra'] = 'pics';
            }
            if($fk=='mpic'){
                if($view=='cmedia'){ $v['title'] = '媒体链接'; }
                $v['vreg'] = ''; $v['vtip'] = '';
            }
            if($fk=='jump'){
                $v['vreg'] = ''; $v['vtip'] = '';
            }
            if($fk=='title' && empty($fmv[$fk])) $fmv[$fk] = $cfgs[$part]; 
            $item = fldView::fitem($fk,$v,$fmv);
            glbHtml::fmae_row($v['title'],$item);
        }
        // tags
        if(isset($cfgs["{$part}_tags"])){
            $ufs = explode(',',$cfgs["{$part}_tags"]);
            $tags = json_decode(@$fmv['tags'],1); 
            foreach ($ufs as $fk) {
                $fmv[$fk] = @$tags[$fk];
                $item = fldView::fitem($fk,$nfs['seo_key'],$fmv);
                $item = str_replace('fm[','tags[',$item);
                glbHtml::fmae_row($fk,$item);
            } 
        }else{
            $item = fldView::fitem('tags',$nfs['seo_key'],@$fmv);
            glbHtml::fmae_row('标签',$item);
        }
        // numbers
        $ufs = array('top'=>'顺序','vote'=>'票数','click'=>'点击'); // ,'show'=>'显示'
        if(!in_array($view,array('cvote'))) unset($ufs['vote']);
        else{ $ufs['click']='刷票率'; }
        $ustr = '';
        foreach ($ufs as $fk=>$title) {
            $v = $nfs['click'];
            $v['fmsize'] = '60';
            if($fk=='top') $v['dbdef'] = '888'; 
            if($fk=='click' && $title=='刷票率') $v['click'] = '5';
            $ustr .= "\n$title".fldView::fitem($fk,$v,@$fmv)."&nbsp; ";
        }
        glbHtml::fmae_row('数字属性',$ustr);
    }

    static function getDno($did='',$cnt=0,$dno=0){
        $def5 = basKeyid::kidRand('',5);
        if($cnt>5 || !is_numeric($dno)){
            $no = $def5;
        }else{
            $whr = "did='$did' AND dno<'9999'";
            $rec = db()->table('topic_items')->where($whr)->order('dno DESC')->find();
            if(empty($rec)){
                $no = '1001';
            }else{ 
                $no = is_numeric($rec['dno']) ? intval($rec['dno'])+1 : $def5; 
                if(strlen("$no")>4) $no = $def5;
            }
        }
        $rec = db()->table('topic_items')->where("did='$did' AND dno='$no'")->find(); 
        if($rec) return self::getDno($did,$cnt+1,$no);
        else return $no;
    }

    static function skip($key){
        $f3 = strpos($key,'_cfgs') || strpos($key,'_tags'); // || strpos($key,'_whrs')
        $f2 = strpos($key,'_begtm') || strpos($key,'_endtm');
        return ($f3 || $f2);
    }

    static function vTitle($data,$title=''){
        if(!empty($data)){
            $title = basStr::cutWidth($data['title'],12);
        }else{
            $title = "($title)";
        }
        if($data['jump']){
            $title = "<a href='{$data['jump']}' target='_blank'>{$title}</a>";
        }
        return $title;
    }

    static function vMpic($data){
        if(!empty($data['mpic'])){
            $mpic = comStore::revSaveDir($data['mpic']);
            $ticon = comFiles::getTIcon($mpic);
            $img = $ticon['icon']=='pic' ? "<img src='$mpic' height='24'>" : 'Open';
            $mpic = "<a href='$mpic' target='_blank'>$img</a>";
        }else{
            $mpic = '-';
        }
        return $mpic;
    }

    // --------------------------------------------------------- 

    static function voteCook($type=''){
        $_rck = comCookie::oget('v_rck');
        if($type=='get'){
            return $_rck;
        }
        if(empty($_rck)){
            $_rnd = basKeyid::kidAuto(12); 
            $_rck = substr(md5($_SERVER["HTTP_USER_AGENT"].$_rnd),0,15).".$_rnd";
            comCookie::oset('v_rck',$_rck,86400);
        }
    }

    static function voteUrlv($did,$vid){
        $_rck = self::voteCook('get');
        $params = "did=$did&vid=$vid&_rck=$_rck";
        $enc = comConvert::sysRevert($params, 0, '_rck', 86400);
        return "$enc";
    }

    static function voteParams(){
        $enc = req('enc'); 
        $str = comConvert::sysRevert($enc, 1, '_rck', 86400);
        $arr = basElm::text2arr($str);
        if(empty($arr['did']) || empty($arr['vid']) || empty($arr['_rck'])){
            $data['error'] = 1;
            $data['msg'] = 'Error(params)!';
            die(out($data,'jsonp'));
        }
        $tmp = explode('.',$arr['_rck']);
        $_rnd = $tmp[1]; 
        $_rck = substr(md5($_SERVER["HTTP_USER_AGENT"].$_rnd),0,15).".$_rnd";
        if($arr['_rck']!=$_rck){
            $data['error'] = 1;
            $data['msg'] = 'Timeout(rck-enc)!';
            die(out($data,'jsonp'));
        }
        return $arr;
    }

    static function moveTmpFiles($row=[], $mod='topic', $kid=''){
        $arr = array('mpic','detail');
        foreach ($arr as $key) {
            if(!empty($row[$key])){
                $ext = $key=='mpic' ? 0 : (strstr($row[$key],'<') ? 1 : 0); 
                $row[$key] = comStore::moveTmpDir($row[$key], $mod, $kid, $ext);
            }
        }
        return $row;
    }

}

/*
// http://book.yunzhan365.com/mwmi/txiv/mobile/index.html
// 湘南学院,《校友通讯》,2017年第二期
*/
