<?php
namespace imcat;

// admMpts(多语言/多城市)相关
class admMpts{    

    // 
    static function vNav($part, $dir){
        global $_cbase;
        $islang = $part=='imcat' && $dir=='lang';
        $istpl = $part=='views' && !in_array($dir,['adm']); // ,'base'
        $isroot = $part=='root' && in_array($dir,['extra','plus']);
        if(!($islang || $istpl || $isroot)){
            return '';
        }
        $fps1 = glob(DIR_IMCAT.'/lang/kvphp/core-*.php');
        $fps2 = glob(DIR_ROOT.'/extra/kvphp/core-*.php');
        $files = array_merge($fps1, $fps2);
        $tra = [];
        foreach($files as $fp) {
            $trkey = str_replace(['core-','.php'],'',basename($fp));
            if($trkey=='cn') continue;
            $tra[$trkey] = $trkey;
        }
        $tab = basElm::setRadio('lang', $tra, '');
        eimp('/~tpl/ftrans.js');
        echo basJscss::jscode("$(function(){ resetNav('$part'); });");
        return "<br>cn ->翻译 $tab";
    }

    // 
    static function vPart($part){
        global $_cbase;
        $cfgs = $_cbase['part'];
        if(isset($cfgs['tab'][$part])){
            return $cfgs['tab'][$part];
        }
        return '(Null)';
    }

    // 同步_各part
    static function syncParts($dop, $kid){
        global $_cbase;
        $cfgs = $_cbase['part'];
        $db = glbDBObj::dbObj();
        $del = 1; 
        $row = $db->table($dop->tbid)->where("did='$kid'")->find();
        if(empty($row)) return;
        $rex = $dop->tbext ? $db->table($dop->tbext)->where("did='$kid'")->find() : 0;
        $psyn = self::dbPsyn($dop, $row);
        $pnow = $row['part']; $otitle = $row['title']; 
        foreach($cfgs['tab'] as $part => $ptitle) {
            if($part==$pnow){
                if($psyn!=$row['psyn']){
                    $tmp = ['psyn'=>$psyn];
                    $db->table($dop->tbid)->data($tmp)->where("did='$kid'")->update();
                }
                $del = 0;
                continue;
            }
            $old = $db->table($dop->tbid)->where("psyn='$psyn' AND part='$part'")->find();
            if(!empty($old)) return;
            $row['did'] = self::dbKid($dop, $row);
            $row['part'] = $part; $row['psyn'] = $psyn;
            $row['title'] = self::fmtTitle($cfgs, $pnow, $part, $otitle);
            $db->table($dop->tbid)->data(in($row))->insert(0);
            if(!empty($rex)){
                $rex['did'] = $row['did'];
                $db->table($dop->tbext)->data(in($rex))->insert(0);
            }
        }
        if($del){
            $db->table($dop->tbid)->where("did='$kid'")->delete();
            $dop->tbext && $db->table($dop->tbext)->where("did='$kid'")->delete();
        }
    }

    // 取消_各part/reset-null
    static function resetParts($dop, $kid=''){
        static $psyns;
        if(empty($psyns)) $psyns = array();
        $db = glbDBObj::dbObj();
        $row = $db->table($dop->tbid)->where("did='$kid'")->find();
        if(empty($row)) return;
        $kid = $row['did'];
        $part = $row['part'];
        $psyn = $row['psyn'];
        if(!$part || !$psyn){
            return;
        }
        if(in_array($psyn,$psyns)){
            return;
        }
        $psyns[] = $psyn;
        $rows = $db->table($dop->tbid)->where("psyn='$psyn'")->select(); // AND did!='$kid'
        $kids = '';
        foreach($rows as $row) {
            if($row['did']==$kid){
                $tmp['part'] = '';
                $tmp['psyn'] = '';
                $db->table($dop->tbid)->data($tmp)->where("did='$kid'")->update();
                continue; 
            }
            $kids .= ($kids ? ',' : '')."'{$row['did']}'";
        }
        if($kids && $dop->tbext){
            $db->table($dop->tbext)->where("did IN($kids)")->delete();
        }
        $kids && $db->table($dop->tbid)->where("did IN($kids)")->delete();
    }

    static function setDef($dop, $kid=''){
        global $_cbase;
        $cfgs = $_cbase['part'];
        $db = glbDBObj::dbObj();
        $old = $db->table($dop->tbid)->where("did='$kid'")->find();
        if(empty($old) || $old['part']==$cfgs['def']) return;
        $tmp['part'] = $cfgs['def'];
        if(in_array($dop->mod,$cfgs['psyn'])){
            $tmp['psyn'] = self::dbPsyn($dop, $old); 
        }
        $db->table($dop->tbid)->data($tmp)->where("did='$kid'")->update();
    }

    static function fmtTitle($cfgs, $pnow, $part, $otitle){
        $isdef = (!$pnow) && ($part==$cfgs['def']);
        if($isdef) return $otitle;
        $pmsg = $cfgs['tab'][$part];
        foreach($cfgs['tab'] as $part => $ptitle) {
            if(strpos($otitle,"[$ptitle]")===0){
                $otitle = substr($otitle,strlen("[$ptitle]"));
            }
        }
        return "[$pmsg]$otitle";
    }

    // dbKid
    static function dbKid($dop, $row, $no=0){
        $db = glbDBObj::dbObj();
        $kid = substr($row['did'],0,10).basKeyid::kidRand('24',$no?3:2);
        $old = $db->table($dop->tbid)->where("did='{$kid}'")->find();
        if(!$old){
            return $kid;
        }else{
            $no++;
            return self::dbKid($dop, $row, $no);
        }
    }
    
    // dbPsyn
    static function dbPsyn($dop, $row, $no=0){
        if(!empty($row['psyn'])){
            return $row['psyn'];
        }
        $db = glbDBObj::dbObj();
        $psyn = basKeyid::kidY3x5().'-'.basKeyid::kidRand('24',$no?4:3);
        $old = $db->table($dop->tbid)->where("psyn='$psyn'")->find();
        if(!$old){
            return $psyn;
        }else{
            $no++;
            return self::dbPsyn($dop, $row, $no);
        }
    }

}

/*

*/
