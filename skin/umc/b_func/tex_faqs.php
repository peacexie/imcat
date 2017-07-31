<?php
/*
单个模板扩展函数
*/ 
class tex_faqs{ //extends tex_base
    
    #protected $prop1 = array();

    static function showMdshow($mdshow,$kid,$rep=0){ 
        if($mdshow=='md'){
            $mdshow = 'Makedown';
        }else{
            $mdshow = 'Text';
        }
        $mod = $rep ? 'qarep' : 'faqs';
        $link = PATH_ROOT."/plus/coms/mdown.php?mod=$mod&kid=$kid";
        $link = "<a href='$link' onclick='return winOpen(this,\"Makedown".lang('user.exf_vcode')."\",600,480);'>$mdshow</a>";
        return $link;
    }

    static function showDetail($detail,$mdshow=''){ 
        if($mdshow=='md'){
            $detail = extMkdown::pdext($detail,0);
        }else{
            $detail = basStr::filText($detail);
        }
        return $detail;
    }


    static function expwhr($obj){ 
        $mcfg = read('faqs'); 
        $arr = array('new','tip','hot','tag',);
        $view = req('view');
        $tag = req('tag');
        $keywd = req('keywd');
        $whr = '';
        if(!empty($obj->key) && isset($mcfg['i'][$obj->key])){
            $whr = " AND catid='$obj->key'";
        }
        if($obj->key=='tip' || $view=='tip'){
            $whr = " AND hinfo>'0'";
        }
        if(!empty($tag)){
            $whr = " AND tags LIKE '%$tag%'";
        }
        if(!empty($keywd)){
            $whr = " AND title LIKE '%$keywd%'";
        }
        if($obj->key=='hot' || $view=='hot'){
            $ord = "click";
        }else{
            $ord = 'did';
        }
        return array($whr,$ord);
    }
    
    static function rndColor(){ 
        $tab = cfg('ucfg.ctab');
        $cArr = explode(',',$tab); 
        $max = count($cArr)-1;
        return $cArr[mt_rand(0,$max)];
    }
    
    static function ejsCfgs($obj){ 
        $mcfg = read('faqs'); 
        $arr = array('new','tip','hot','tag',);
        $view = req('view');
        $tag = req('tag');
        if(empty($obj->key)){
            $sub = '_allt';
        }elseif(isset($mcfg['i'][$obj->key])){
            $sub = $obj->key;
        }else{
            $sub = '_allt';
        }
        if($tag){
            $top = 'tag';
        }elseif($obj->key && in_array($obj->key,$arr)){
            $top = $obj->key;
        }elseif($view && in_array($view,$arr)){
            $top = $view;
        }else{
            $top = 'new';
        }
        $jstr = "var qas_id='$sub', qat_id='$top';";    
        return $jstr;
    }
    
    static function navTags($obj,$tags=''){ 
        if(empty($tags)) return '';
        $tags = explode(',',$tags); 
        $str = ''; 
        foreach($tags as $tag){ 
            if(empty($tags)) continue;
            $str .= "<a href='".surl(0)."?mkv=faqs--list&tag=$tag' class='c".tex_faqs::rndColor()."'>$tag</a>";
        }
        return $str;
    }
    
    static function navTop($obj,$re='html'){ 
        $mcfg = read('faqs'); 
        $cfg = basLang::ucfg('cfgbase.ucfaqn4');
        $tag = req('tag');
        $tag && $cfg['tag'] .= ":$tag";
        $str = '';
        foreach($cfg as $key=>$val){
            if($key=='tag'){
                $url = surl("faqs-$key");
            }elseif(empty($obj->key)){
                $url = $key=='new' ? surl('faqs') : surl("faqs-$key");
            }elseif(isset($mcfg['i'][$obj->key])){
                $url = surl(0)."?mkv=faqs-$obj->key&view=$key";
            }else{
                $url = $key=='new' ? surl('faqs') : surl("faqs-$key");
            }
            $str .= "<a href='$url' id='qat_$key'>$val</a>";
        }
        return $str;
    }
    
    static function navSide($obj,$re='html'){ 
        $mcfg = read('faqs'); 
        $stats = self::statTypes($act='get');
        $str = "<a href='".surl('faqs')."' id='qas__allt'><i class='right'>".$stats['_allt']."</i>".lang('user.exf_alltype')."</a>";
        $arr = array('_allt'=>lang('user.exf_alltype'));
        foreach($mcfg['i'] as $key=>$val){
            $cnts = "<i class='right'>".(empty($stats[$key]) ? 0 : $stats[$key])."</i>";
            $str .= "<a href='".surl("faqs-$key")."' id='qas_$key'>$cnts$val[title]</a>";
            $arr[$key] = $val['title'];
        }
        return $re=='html' ? $str : $arr;
    }
    
    // 统计类别下的问答
    static function statTypes($act='get'){ 
        $arr = array();
        $file = "/store/_faqs_types.cfg.php";
        $arr = extCache::tkGet('faqs_types',1);
        if(empty($arr)){ 
            $act = 'upd';
        }
        $db = db(); 
        $tabfull = "{$db->pre}docs_faqs{$db->ext}"; 
        $arr['_tags'] = $db->table('coms_qatag')->where("`show`='1'")->count(); 
        $arr['_allt'] = $db->table('docs_faqs')->where("`show`='1'")->count(); 
        $q = $db->query("SELECT catid,count(*) as cnt FROM $tabfull WHERE `show`='1' GROUP BY catid"); 
        foreach($q as $k=>$v){
            $arr[$v['catid']] = $v['cnt'];
        }
        if($act=='upd'){
            extCache::tkSet('faqs_types',$arr,30);
        }
        return $arr;
    }
    
    // 统计/重置标签
    static function statTags($act='get'){ 
        $db = db(); 
        $list = $db->field('tags')->table('docs_faqs')->where("`show`='1'")->select();
        $at = array(); 
        foreach($list as $tags){
            if(empty($tags['tags'])) continue;
            $arr = explode(',',$tags['tags']);
            foreach($arr as $tag){
                if(empty($tag)) continue;
                if(isset($at[$tag])) $at[$tag] += 1;
                else $at[$tag] = 1;
            } 
        }
        $no = 100;
        $db->table('coms_qatag')->where("cid>'0'")->delete(); 
        foreach($at as $tag=>$cnt){ 
            $no += mt_rand(7,13);
            $cid = basKeyid::kidTemp('0').'-'.basKeyid::fmtBase32('',$no,32,5); 
            $data = array('cid'=>$cid,'cno'=>'1','title'=>$tag,'show'=>'1','hotcnt'=>$cnt,);
            $db->table('coms_qatag')->data($data)->insert(); 
        }
    }
    
}

/*

*/
