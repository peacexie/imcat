<?php
namespace imcat;

// 后台函数; 
class exaHouse{

    static function delRels($type,$did){
        // album
        $whr1 = "catid='$type' AND lpid='$did'";
        self::delRel1('docs_album',$whr);
        // house
        if($type=='house'){
            // huxing
            $whr2 = "lpid='$did'";
            self::delRel1('docs_huxing',$whr);
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
        $row = db()->tabel('docs_'.$type)->where("did='$did'")->find; 
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
