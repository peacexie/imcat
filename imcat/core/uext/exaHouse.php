<?php
namespace imcat;

// 后台函数; 
class exaHouse{

    // 
    static function getLprow($lp, $mod='house', $len=12){
        $db = glbDBObj::dbObj();
        $pcfgs = ['sale'=>'出售', 'rent'=>'出租']; 
        $pname = isset($pcfgs[$mod]) ? $pcfgs[$mod] : '楼盘';
        $null = ['lparea'=>"<i class='cCCC'>区域</i>",'lplink'=>"<i class='cCCC'>未知{$pname}</i>"];
        if(empty($lp)) return $null; // is_array
        $row = $db->table("docs_$mod")->where("did='$lp'")->find();
        if(!$row){ return $null; }
        $arid = $row['areas'];
        $local = read('local.i'); // $cv->TKeys($row,0,'areas',12,"-");
        if(!empty($local[$arid]['pid'])){
            $pid = $local[$arid]['pid'];
            $lparea = $local[$pid]['title'];
        }elseif(!empty($local[$arid])){
            $lparea = $local[$arid]['title'];
        }else{
            $lparea = "区域";
        }
        $title = basStr::cutWidth($row['title'],$len);
        $row['lparea'] = "<i class='cCCC'>$lparea</i>";
        $row['lplink'] = "<a href='".surl("comm:house.$row[did]")."' class='c666' target='1'>$title</a>";
        return $row;
    }

    // cache: '0'-重新计算, '1':缓存, '-1'-自动
    static function getRCnts($data, $cache='-1', $mod='house'){
        $db = glbDBObj::dbObj();
        if(is_array($data)){
            $row = $data;
        }else{
            $row['did'] = $data;
        }
        $cfgs = ['cntpic'=>'album']; // 相册, cntld=楼栋数; cnthu=总户数;
        if($mod=='house'){ // $mod=='house', $row['catid']=='lp', 
            $cfgs['cnthx'] = 'huxing'; // 户型
            $cfgs['cntnews'] = 'hnews'; // 动态
            $cfgs['cntcs'] = 'sale'; // 出售
            $cfgs['cntcz'] = 'rent'; // 出租
        }
        $res = $upd = [];
        foreach($cfgs as $ik => $iv){
            $old = isset($row[$ik]) ? $row[$ik] : 0;
            if(!$cache || (!$old && $cache=='-1')){
                $whrc = $ik=='album' ? "part='$mod' AND " : '';
                $old = $db->table("docs_$iv")->where("{$whrc}lpid='$row[did]'")->count();
                $upd[$ik] = $old; // if($old) 
            }
            $res[$ik] = $old;
        }
        if(!empty($upd)){
            $db->table("docs_$mod")->data($upd)->where("did='$row[did]'")->update(0);
        }
        return $res;
    }

    static function delRels($type,$did){
        // album
        $whr1 = "part='$type' AND lpid='$did'";
        self::delRel1('docs_album',$whr1);
        // house
        if($type=='house'){
            // huxing
            $whr2 = "lpid='$did'";
            self::delRel1('docs_huxing',$whr2);
            // fy
            db()->table('docs_sale')->data(['lpid'=>$did])->where($whr2)->update();
            db()->table('docs_rent')->data(['lpid'=>$did])->where($whr2)->update();
            // hnews
            db()->table('docs_hnews')->where($whr2)->delete();
            // ....
        }else{
            // 更新统计?
        }
        // self-pic
        $row = db()->table('docs_'.$type)->where("did='$did'")->find(); 
        self::delPic1($row);
    }

    static function delRel1($tab,$whr){
        #if(!$whr) return;
        $list = db()->table($tab)->where($whr)->select();
        foreach($list as $key=>$row) {
            self::delPic1($row);
        }
        db()->table($tab)->where($whr)->delete();
    }    
   
    static function delPic1($row){
        if(!$row) return;
        $pic = $row['mpic'];
        if(strpos($pic,'uresroot}/mdown')>0){
            @unlink(str_replace('{uresroot}',DIR_URES,$pic));
        }
    }

}
