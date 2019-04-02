<?php
namespace imcat;

// DBExt
class glbDBExt{    
    
    // 字段 - 添加/删除/修改 
    static function setOneField($mod,$cid,$act='del',$cfg=array()){
        $_groups = glbConfig::read('groups');
        $db = glbDBObj::dbObj();
        $tabf = 'base_fields';
        $r = $db->table($tabf)->where("model='$mod' AND kid='$cid'")->find();
        if(in_array($r['dbtype'],array('nodb','file'))) return; 
        $tabid = self::getTable($mod,empty($r['etab'])?'0':1); 
        $cols = $db->fields($tabid); 
        if($act=='del'){
            if(isset($cols[$cid])) $db->query("ALTER TABLE $db->pre{$tabid}$db->ext DROP `$cid` ");
            $db->table($tabf)->where("model='$mod' AND kid='$cid'")->delete();     
        }else{
            $sql = "ALTER TABLE $db->pre{$tabid}$db->ext";
            if(isset($cols[$cid])) $sql.= " CHANGE `$cid` ";
            else                   $sql.= " ADD ";     
            if(empty($r) && !empty($cfg)) $r = $cfg;
            $dblen = intval(@$r['dblen']);
            $sql.= " `$cid` $r[dbtype]".($r['dbtype']=='varchar' ? ($dblen>0?"($dblen)":'(12)') : ''); 
            $sql.= (strpos("($r[vreg]",'nul:') ? " NULL " : ' NOT NULL '); 
            //$sql.= (empty($r['dbdef']) ? "" : " DEFAULT '$r[dbdef]' "); 
            if(strstr($r['dbtype'],'char')){
                $sql.= " DEFAULT '".(strlen($r['dbdef'])==0?'':$r['dbdef'])."' "; 
            }elseif(strstr($r['dbtype'],'int')){
                $sql.= " DEFAULT '".(strlen($r['dbdef'])==0?'0':$r['dbdef'])."' "; 
            }
            $after = self::findAfterField($cols,$cid);
            if(!isset($cols[$cid])) $after && $sql.= " AFTER `$after` ";
            $db->query($sql);
        }
    }

    static function setfieldDemo($mod,$obj,$org='(drop)'){
        $db = glbDBObj::dbObj();
        if($org=='(drop)'){
            $db->query("DROP TABLE IF EXISTS $db->pre{$obj}$db->ext ");
            $db->table('base_fields')->where("model='$mod'")->delete(); 
        }else{ 
            $_ta = explode('_',$org);
            $obj && $db->query("CREATE TABLE IF NOT EXISTS $db->pre{$obj}$db->ext LIKE $db->pre{$org}$db->ext");
            if(in_array($_ta[0],array('coms','docs','users'))){ //增加默认字段配置'dext',
                $pid = $_ta[1]; 
                $_cfg = glbConfig::read($pid);
                $farr = @$_cfg['f']; 
                $top = 120; 
                if($farr){ foreach($farr as $k=>$v){
                    $tabid = 'base_fields';
                    if(!$db->table($tabid)->where("kid='$k' AND model='$mod'")->find()){
                        $fm = array('kid'=>$k,'model'=>$mod,'top'=>$top,)+$v; 
                        $db->table($tabid)->data($fm)->insert();
                    }
                    $top += 4; 
                } }
            }
        }
    }
    
    // tab下-添加字段，在什么字段后面
    static function findAfterField($tab,$col){
        $_groups = glbConfig::read('groups');
        $a = is_array($tab) ? $tab : $db->fields($tab);
        if(isset($_groups[$col]) && $_groups[$col]['pid']=='types' && isset($a['catid'])){
            $def = 'catid';
        }else{
            $def = ''; $bak = ''; 
            foreach($a as $k=>$v){
                if($k=='aip'){ 
                    $def = empty($bak) ? 'aip' : $bak;
                    break;
                }
                if(substr("$k)))",0,3)==substr("$col)))",0,3)){
                    $def = $k;
                }
                $bak = $k;
            }
        }
        return $def;
    }
        
    /* *****************************************************************************
      *** 数据库相关函数 
    - db前缀
    - by Peace(XieYS) 2012-07-23
    ***************************************************************************** */
    // tab, $tmp(md2,md3,mdh),max,
    static function dbAutID($tab='utest_keyid',$tmp='max',$n=0){
        $db = glbDBObj::dbObj();
        $tabf = $db->pre.$tab.$db->ext;
        $tfix = substr($tab,0,5); 
        if(in_array($tfix,array('docs_','coms_','advs_','users'))){
            $tkey = substr($tfix,0,1).'id';
            $tno = substr($tfix,0,1).'no';
        }else{
            $tkey = 'kid'; 
            $tno = 'kno';
        }
        $ktmp = basKeyid::kidTemp($tmp); // 2018-mdh-1233
        if($tmp=='max'){
            $kno = 1;
            $kid = $ktmp;
        }else{
            $pdb = substr($ktmp,0,strrpos($ktmp,'-')).'%';
            $mdb = $db->query("SELECT max($tno) as $tno FROM $tabf WHERE $tkey LIKE '$pdb'");
            $kno = empty($mdb[0][$tno]) ? 1 : ($mdb[0][$tno]+1) % 99;
            $kfix = $kno<10 ? '0'.$kno : $kno;
            $kid = substr($ktmp,0,strlen($ktmp)-2).$kfix;
        }
        if($n) $kid = substr($kid,0,strlen($kid)-2).basKeyid::kidRand('k',2);
        $rec = $db->table($tab)->where("$tkey='$kid%'")->find();
        if($rec) return self::dbAutID($tab,$tmp,$n+1);
        else return array($kid,$kno);
    }

    static function dbNxtID($tab,$mod,$pid='0'){
        $sqlm = $tab=='bext_relat' ? '' : ($tab=='base_model' ? 'pid' : 'model')."='$mod' AND ";
        $mcfg = glbConfig::read($mod); //"$sqlm kid REGEXP ('^{$fix}[0-9]{3}$')";
        $fd = $pid ? ($mcfg['i'][$pid]['deep']+1) : '1';
        $tid = ''; // 找:本pid下最大的一个ID
        if(!empty($mcfg['i'])){ foreach($mcfg['i'] as $ik=>$iv) {
            if($iv['pid']==$pid && preg_match("/^[a-z]{1,6}\d{2,10}$/i",$ik)){
                $tid = max($tid,$ik);
            }
        } }
        if($tid){ // 找:所有的类似最大的一个ID
            preg_match("/^([a-z]{1,5})(\d{2,10})$/i",$tid,$tmp); 
            $fix = $tmp[1]; $no = $tmp[2]; $nl = strlen($no);
            foreach($mcfg['i'] as $ik=>$iv) {
                if($iv['pid']==$pid) continue;
                if($iv['deep']!=$fd) continue;
                if(preg_match("/^$fix\d{{$nl}}$/i",$ik)){
                    $tid = max($tid,$ik);
                }
            } //echo $tid;
            // 找:下一个
            preg_match("/^([a-z]{1,5})(\d{2,10})$/i",$tid,$tmp);
            $nid = $fix.basKeyid::kidNext('',$tmp[2],$tmp[2],2,strlen($tmp[2]));
            // 是否存在
            $whr = "$sqlm kid='$nid'"; // echo "(n=$nid";
            $re = glbDBObj::dbObj()->table($tab)->where($whr)->order('kid DESC')->find();
            if(!$re){ return $nid; }
        }
        $fix = substr($mod,0,1).$fd;
        $whr = "$sqlm kid LIKE '$fix%'";
        $re = glbDBObj::dbObj()->table($tab)->where($whr)->order('kid DESC')->find();
        if($re){
            $nid = substr($re['kid'],2);
            $nid = basKeyid::kidNext('',$nid,'012',2,3);
        }else{ $nid = '012'; }
        return $fix.$nid;
    }
    
    // ext: 0-tab, 1-ext, kid, arr
    static function getTable($mod,$ext='0'){ 
        $_groups = glbConfig::read('groups');
        if(!isset($_groups[$mod])) return '';
        if($_groups[$mod]['pid']=='docs'){
            $tabid = $ext==1 ? 'dext_'.$mod : 'docs_'.$mod;
            $keyid = 'did';
        }elseif($_groups[$mod]['pid']=='users'){
            $tabid = 'users_'.$mod;
            $keyid = 'uid';
        }elseif($_groups[$mod]['pid']=='advs'){
            $tabid = 'advs_'.$mod;
            $keyid = 'aid';
        }elseif($_groups[$mod]['pid']=='coms'){    
            $tabid = 'coms_'.$mod;
            $keyid = 'cid';
        }elseif($_groups[$mod]['pid']=='types'){    
            $tabid = empty($_groups[$mod]['etab']) ? 'types_common' : 'types_'.$mod;
            $keyid = 'kid';
        }else{
            $tabid = $keyid = '';    
        }
        if(is_numeric($ext)){ // 0, 1
            return $tabid;
        }else{ // kid,arr
            return $ext=='kid' ? $keyid : array($tabid,$keyid);
        }
    }
    
    static function getKids($mod,$kid='',$whr='',$ret='sub'){
        $tabid = self::getTable($mod); 
        $kid = $kid ? basStr::filKey($kid,'_-.') : self::getKeyid($mod); 
        $list = glbDBObj::dbObj()->table($tabid)->field($kid)->where($whr)->select();
        $re = array();
        if($list){
        foreach($list as $r){
            $re[] = $r[$kid];
        } }
        if(empty($re)){
            $re[] = '(null)';    
        }
        if($ret=='sub'){
            $re = "'".implode("','",$re)."'";
        }
        return $re;
    }
    
    static function dbComment($tabid='~return~'){ 
        static $dbdict,$fmod,$fdemo;
        $db = glbDBObj::dbObj();
        $fsystem = basLang::ucfg('fsystem');
        if(empty($dbdict)){
            $dict = $db->table('bext_dbdict')->field("kid,tabid,title")->select();
            foreach($dict as $v){
                $dbdict[$v['tabid']][$v['kid']] = $v['title'];
            }
        }
        if(empty($fmod)){
            $dict = $db->table('base_fields')->field("kid,model,title")->select();
            foreach($dict as $v){
                $fmod[$v['model']][$v['kid']] = $v['title'];
            }
        }    
        if(empty($fdemo)){
            $fdemo = array();
            $demo = glbConfig::read('fdemo','sy');
            foreach(array('init_users','init_coms','init_dext','init_docs',) as $part){
                $fpart = $demo[$part];
                foreach($fpart as $f=>$v){
                    $fdemo[$f] = $v['title'];
            } }
        }
        if($tabid=='~return~') return array('fsystem'=>$fsystem,'fdemo'=>$fdemo,);
        $fields = $db->fields($tabid);
        $moda = explode('_',$tabid); 
        $modid = $moda[1];
        foreach($fields as $f=>$v){
            $flag = 'def'; $rem = '';
            if(isset($fmod[$modid][$f])){ //模型设置
                $flag = 'mod';
                $rem = $fmod[$modid][$f];
            }elseif(empty($dbdict[$tabid][$f])){ //dbdict为空
                if(isset($fdemo[$f])){
                    $flag = 'demo';
                    $rem = $fdemo[$f];
                }
                if(isset($fsystem[$f])){
                    $flag = 'sys';
                    $rem = $fsystem[$f];
                }
            }else{ //dbdict设置
                $rem = $dbdict[$tabid][$f];    
                if(isset($fsystem[$f]) || in_array($moda[0],array('active','advs','base','bext','logs'))){
                    $flag = 'sys';
                }
            }
            $fields[$f]['_flag'] = $flag;
            $fields[$f]['_rem'] = basStr::filTitle($rem);
        }
        $_groups = glbConfig::read('groups');
        if(isset($_groups[$modid]) && $_groups[$modid]['pid']==$moda[0]){
            $fields[0]['_flag'] = 'sys'; 
            $cfg = basLang::ucfg('cfglibs.dbext');
            $fields[0]['_rem'] = $_groups[$modid]['title'].('['.$cfg[$moda[0]].']').basLang::show('dbdict_tab');
        }
        if(isset($_groups[$modid]) && $moda[0]=='dext'){
            $fields[0]['_flag'] = 'sys';
            $fields[0]['_rem'] = $_groups[$modid]['title'].basLang::show('dbdict_extab');
        }
        if(isset($fsystem['_stabs'][$tabid])){
            $fields[0]['_flag'] = 'sys';
            $fields[0]['_rem'] = $fsystem['_stabs'][$tabid];
        }
        if(empty($fields[0]['_rem'])&& !empty($dbdict[$tabid][0])){
            $fields[0]['_flag'] = '';
            $fields[0]['_rem'] = $dbdict[$tabid][0];
        }
        return $fields;
    }
    
    // 获取一组扩展参数
    static function getExtp($type){ 
        $data = array();
        $whr = strpos($type,'%') ? " LIKE '$type'" : "='$type'";
        $list = glbDBObj::dbObj()->table('bext_paras')->where("pid$whr AND enable=1")->order('top')->limit(99)->select();
        if($list){ foreach($list as $i=>$r){ 
            $r['i'] = $i+1;
            foreach(array('aip','atime','auser','eip','etime','euser','cfgs','note','enable') as $k2){
                unset($r[$k2]);
            }
            $data[$r['kid']] = $r;
        } } 
        return $data;
    }

}
