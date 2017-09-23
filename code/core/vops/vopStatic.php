<?php

// Static相关
class vopStatic{

    //生成一个广告类别的缓存
    static function advType($mod,$type=''){
        $cfg = glbConfig::read($mod);
        $dtpl = array(
            '1'=> "<a href='{url}' target='_blank'>{title}</a>",
            '2'=> "<a href='{url}' target='_blank'><img src='{mpic}' name='{title}' alt='{title}' /></a>",
            '3'=> "{detail}",
        );
        $tpl = empty($cfg['i'][$type]['cfgs']) ? $dtpl[$cfg['etab']] : $cfg['i'][$type]['cfgs']; 
        if(!strpos($tpl,'target')) $tpl = str_replace('<a',"<a target='_blank'",$tpl);
        $data = ''; $rep = array('title','url','mpic','detail');
        $list = glbDBObj::dbObj()->table("advs_$mod")->where("catid='$type' AND `show`='1'")->select();
        if($list){
            foreach($list as $r){
                $istr = $tpl;
                if(in_array($cfg['etab'],array(1,2))){ 
                    $r['url'] = PATH_ROOT."/plus/ajax/redir.php?advs:".comConvert::sysBase64("$mod,$r[aid],$r[url]");
                }
                foreach($rep as $key){
                    $istr = str_replace('{'.$key.'}',$r[$key],$istr);
                }
                $istr = str_replace(array('{root}','{$root}'),PATH_ROOT,$istr);
                if($cfg['etab']==3 && !empty($r['url'])){
                    $_arep = explode('[=]',$r['url']); 
                    $istr = str_replace($_arep[0],$_arep[1],$istr);
                }
                $data .= "$istr\r\n";
            }
        }
        $data = comStore::revSaveDir($data);
        $file = tagCache::caPath($mod,$type,0);
        comFiles::chkDirs($file,'ctpl'); 
        comFiles::put(DIR_CTPL."/$file",$data);
        return $data;
    }

    //广告Static(按类别)
    static function advMod($mod,$aids=array()){
        $cfg = glbConfig::read($mod);
        if($cfg['etab']==4) return;
        if($mod=='adpush') return;
        $ids = '';
        if(is_array($aids)){
            foreach($aids as $id=>$v){ 
                if(empty($id)) continue;
                $ids .= (empty($ids) ? '' : ',')."'$id'";            
            }
        }else{
            $ids = "'$aids'";    
        }
        if(!$ids) return;
        $whr = $ids=="'(all)'" ? '1=1' : "aid IN($ids)";
        $types = array(); $data = '';
        $list = glbDBObj::dbObj()->field('DISTINCT catid')->table("advs_$mod")->where($whr)->select(); //frame=0 AND 
        if($list){
            foreach($list as $r){
                $caid = $r['catid'];
                $ditm = self::advType($mod,$caid);
                $data .= "[$caid]-{$cfg['i'][$caid]['title']}:::<br>$ditm <hr class='ma10 cF0F'> ";
            }
        }
        return $data;
    }

    // showRes
    static function showRes($res){
        $msg = basLang::show('core.vops_batres')."<br><br>\n";
        $msg .= $res['msg'] ? $res['msg']."<br>" : basLang::show('core.vops_dores',"[{$res['ok']}/{$res['cnt']}]")."<br>\n";
        $msg .= "[".date('Y-m-d H:i:s')."]<br>\n";
        if($res['next']){ 
            $msg .= "<br>\n".basLang::show('core.vops_3secnext')."<br>\n";
            $js = "setTimeout(\"location.href='{$res['url']}';\",3000);"; 
            $js = basJscss::jscode($js);
        }else{
            $msg .= "<br>\n".basLang::show('core.vops_end')."<br>\n";
            $js = '';    
        }
        glbHtml::page(basLang::show('core.vops_res'));
        glbHtml::page('body');
        echo "\n<p>$msg</p>\n<hr>\n$js";
        unset($res['msg']); basDebug::varShow($res);
        glbHtml::page('end');
    }
    
    static function batList($mod,$tpl){ 
        $re = array('msg'=>'','cnt'=>0,'ok'=>0,'next'=>'','min'=>'','max'=>'',);
        $lists = vopTpls::entry($tpl,'ehlist','static');
        $listn = $lists[$mod]; $msg = '';
        foreach($listn as $key=>$v){ 
            if(!strpos($v,'/')) continue; //first,close
            $key = $key=='m' ? "" : "-$key";
            $msg .= vopStatic::toFile("$mod$key")."<br>\n";     
        }
        $re['msg'] = $msg;
        return $re;
    }
    static function batDetail($mod,$tpl){ 
        $re = array('msg'=>'','cnt'=>0,'ok'=>0,'next'=>'','min'=>'','max'=>'',);
        $data = self::batKids($mod); $msg = ''; 
        $vcfg = glbConfig::vcfg($mod);
        if(!empty($data)){
            foreach($data as $key=>$row){ 
                $oid = $key;
                if(empty($re['min']) || $oid<$re['min']) $re['min'] = $oid;
                if(empty($re['max']) || $oid>$re['max']) $re['max'] = $oid;
                if(empty($vcfg['c']['stypes']) || in_array($row['type'],$vcfg['c']['stypes'])){ 
                    $da = is_array($vcfg['d']) ? $vcfg['d'] : array('0'=>1); 
                    foreach ($da as $ka => $va) {
                        $msg .= vopStatic::toFile("$mod.$key".(empty($ka)?'':".$ka"))."<br>\n";
                    }
                    $re['cnt']++;
                }else{
                    $msg .= "[$mod.$key][{$row['type']}] Skip...<br>\n";
                }
            }
            $re['next'] = 1; 
        }else{
            $re['msg'] = 'No Data';
            $re['next'] = 0;    
        } 
        $re['msg'] = $msg;
        if($re['next']){
            $offset = basReq::val('order')=='ASC' ? $re['max'] : $re['min'];
            $re['url'] = basReq::getURep(0,'offset',$offset);
        }
        return $re; 
    }
    // mod,limit(1-500),order(did:ASC),stype,offset
    static function batKids($mod){
        $mcfg = glbConfig::read($mod); $whrstr = '';
        $stype = basReq::val('stype'); $offset = basReq::val('offset');
        $limit = basReq::val('limit',10,'N'); if($limit<1) $limit = 10;
        $order = basReq::val('order','DESC');
        if(in_array($mcfg['pid'],array('docs','advs'))){ 
            $stype && $whrstr .= basSql::whrTree($mcfg['i'],'catid',$stype);
            $ftype = ',catid';    
            $tabid = "docs_$mod";
        }elseif(in_array($mcfg['pid'],array('users'))){
            $stype && $whrstr .= " AND grade='$stype'";
            $ftype = ',grade';    
            $tabid = "users_$mod";
        }else{
            $ftype = '';    
        }
        $kname = substr($mcfg['pid'],0,1).'id';
        $omod = in_array(strtoupper($order),array('ASC','DESC','EQ')) ? strtoupper($order) : 'DESC';
        $offset = basReq::val('offset');
        if($offset){
            if(strstr($omod,'DESC')){
                $op = '<';
            }elseif(strstr($omod,'ASC')){
                $op = '>';
            }else{ // EQ
                $op = '=';
            }
            $whrstr .= " AND $kname$op'$offset'";
        } 
        $whrstr = $whrstr ? substr($whrstr,5) : '1=1'; 
        $data = glbDBObj::dbObj()->field("$kname$ftype,`show`")->table($tabid)->where($whrstr)->order("$kname $omod")->limit($limit)->select(); 
        $re = array();
        if(!empty($data)){
            foreach($data as $row){ 
                $type = $ftype ? $row[substr($ftype,1)] : $ftype;
                $re[$row[$kname]] = array('show'=>$row['show'],'type'=>$type);
            }
        }
        return $re;
    }
    
    static function delList($mod,$tpl){ 
        $re = array('msg'=>'','cnt'=>0,'ok'=>0,'next'=>'','min'=>'','max'=>'',);
        $lists = vopTpls::entry($tpl,'ehlist','static');
        $listn = $lists[$mod]; $msg = '';
        foreach($listn as $key=>$v){
            $key = $key=='m' ? "" : $key;
            $file = self::getPath($mod,$key,0);
            $res = @unlink(DIR_HTML.'/'.$file); $res = var_export($res,1);
            $msg .= "{$res}:".$file."<br>\n";
        }
        $re['msg'] = $msg;
        return $re;
    }
    static function delDetail($mod,$sub=''){ 
        static $fext, $msg;
        if(empty($fext)){
            $mcfg = glbConfig::vcfg($mod); 
            $fext = $mcfg['c']['stext'];
            $msg = '';
        } 
        $dfix = basReq::val('offset');
        $elen = strlen($fext);
        $ndir = DIR_HTML."/$mod$sub";
        if(!is_dir("$ndir")){ return false; };
        $handle = opendir("$ndir");
        while(($file = readdir($handle)) !== false){
            if($file != "." && $file != ".."){
                if(in_array($file,array('index.htm','index.html'))) continue;
                if(is_dir("$ndir/$file")){
                    if(empty($sub) && $file=='home'){ continue; }
                    if(empty($sub) && $dfix && strstr($file,$dfix)){ continue; }
                    self::delDetail($mod,"$sub/$file");
                }else{
                    if(substr($file,-$elen,$elen)==$fext){
                        $res = @unlink("$ndir/$file"); $res = var_export($res,1);
                        $msg .= "{$res}:$sub/$file<br>\n";
                    }
                }
            }
        }
        $re = array('cnt'=>substr_count($msg,'<br>'),'ok'=>substr_count($msg,'true:'),'next'=>'',);
        $re['msg'] = $msg;
        return $re;
    }
    //生成Static文件(当前模板)
    static function toFile($q=''){ 
        basEnv::runCbase(); //重置参数
        defined('RUN_STATIC') || define('RUN_STATIC',1);
        static $vop; if(empty($vop)) $vop = new vopShow(0);
        ob_start(); 
        $err = $vop->run($q); 
        if(empty($vop->ucfg)){ 
            return $vop->err ? $vop->err : "($q)Error! Has NO set tpl!"; 
        }
        $re = ob_get_contents();
        $kid = $vop->mod=='home' ? 'home' : $vop->key;
        $vext = empty($vop->view) ? '' : ".$vop->view";
        $file = self::getPath($vop->mod,$kid.$vext,0); 
        $msg = $file.' : '.basStr::showNumber(strlen($re),'Byte').'';
        comFiles::chkDirs($file,'html');
        comFiles::put(DIR_HTML.'/'.$file,"$re\n<!--$msg-->");
        ob_end_clean(); 
        return $msg;
    }
    
    //获得Static文件路径(当前模板):
    static function getPath($mod,$kid,$isfull=1){
        $kid || $kid = 'home';
        $dir = comStore::getResDir($mod,$kid,$isfull);
        if($isfull){
            $dir = DIR_HTML.substr($dir,strlen(DIR_URES));     
        }
        $mcfg = glbConfig::vcfg($mod);
        return $dir.(empty($mcfg['c']['stext']) ? '.htm' : $mcfg['c']['stext']);
    }
    
    //updKid:更新(add,del,edit)一个Kid的静态
    static function updKid($mod,$kid,$act='upd',$itype=''){
        global $_cbase;
        $tplbak = $_cbase['tpl']['tpl_dir'];
        $vcfg = vopTpls::etr1('tpl'); 
        $re = array();
        foreach($vcfg as $tpl=>$suit){
            if(strpos($_cbase['tpl']['no_static'],$tpl)) continue;
            $file = DIR_SKIN."/$tpl/_config/vc_$mod.php";
            if(file_exists($file)){
                include $file; 
                $kc = "_vc_$mod"; $cfg = $$kc;
                if($itype && !empty($cfg['c']['stypes']) && !in_array($itype,$cfg['c']['stypes'])){ 
                    continue; // 忽略不需要生成静态的页面
                } 
                if($cfg['c']['vmode']=='static'){
                    if($act=='del'){
                        $_cbase['tpl']['tpl_dir'] = $tpl; 
                        $re[$tpl] = self::getPath($mod,$kid,1);
                    }else{
                        $re[$tpl] = "$mod.$kid";
                    }
                }
            }
        }
        $_cbase['tpl']['tpl_dir'] = $tplbak;
        return $re; 
    }
    
    //是否需要生成静态
    static function chkNeed($mkv=''){ 
        $moda = vopUrl::imkv(array('mkv'=>$mkv),'a');
        $kid = $moda[0]=='home' ? 'home' : $moda[1]; 
        $vcfg = glbConfig::vcfg($moda[0]); 
        if(@$vcfg['c']['vmode']!='static') return 0;
        $vext = empty($moda[2]) ? '' : ".$moda[2]";
        $file = self::getPath($moda[0],$kid.$vext,0); 
        $flag = !extCache::cfGet("/$file",$vcfg['c']['stexp'],'html');
        return $flag;    
    }

}