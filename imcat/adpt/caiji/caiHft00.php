<?php
namespace imcat;

// ...类 caiHft00
class caiHft00{

    // house,Fy,huxing,album

    static function fixFy($type='sale'){
        $local = caiBase::attrs('local.i'); 
        $cnt = 0; 
        $list = db()->table('docs_'.$type)->where("lpid LIKE 'hft-%'")->limit(50)->select();
        if($list){ 
            foreach($list as $row) {
                $url = str_replace('h_160,w_220','h_600,w_800',$row['mpic']);
                $res = caiBase::savePic($url, $type); 
                $upd2['mpic'] = $res;
                $upd1 = []; $tmp = explode(',',$row['sotags']);
                if(!empty($tmp[2])){
                    $old = db()->table('docs_house')->where("title='{$tmp[2]}'")->find();
                    if(!$old){
                        if(strlen($row['map'])>15){
                            $upd1['map'] = $row['map'];
                        }
                        $upd1['title'] = $tmp[2];
                        $upd1['catid'] = 'xq';
                        $upd1['areas'] = caiBase::atkeys($local['0'],$tmp[0],2);
                        $upd1['addr'] = $row['addr'];
                        $upd1['wytype'] = $row['wytype'];
                        $upd1['zxtype'] = $row['zxtype'];
                        $upd1['exid'] = $row['lpid'];
                        $kar = [caiBase::dbKid('docs_house',$row['dno']),$row['dno']];
                        $upd1['did'] = $kar[0]; $upd1['dno'] = $kar[1];
                        $upd2['lpid'] = $upd1['did'];
                        db()->table('docs_house')->data($upd1)->insert();
                    }else{
                        $upd2['lpid'] = $old['did'];
                    }
                }else{
                    $upd2['lpid'] = '';
                }
                db()->table('docs_'.$type)->where("did='{$row['did']}'")->data($upd2)->update();
                $cnt++;
            } 
        }
        return $cnt;
    }

    static function fixHouse(){
        $cnt = 0; 
        $list = db()->table('docs_house')->where("mpic LIKE '//pic.haofang.net%'")->limit(50)->select();
        if($list){ 
            foreach($list as $row) {
                $url = str_replace('h_206,w_280','h_600,w_800',$row['mpic']);
                $res = caiBase::savePic($url, 'house'); 
                $upd2 = ['mpic'=>$res];
                db()->table('docs_house')->where("did='{$row['did']}'")->data($upd2)->update();
                $cnt++;
            } 
        }
        return $cnt;
    }

    static function fixHuxing(){
        $cnt = 0; 
        $list = db()->table('docs_huxing')->where("aflag=8")->limit(50)->select();
        if($list){ 
            foreach($list as $row) {
                $url = str_replace('h_230,w_270','h_600,w_800',$row['mpic']);
                $res = caiBase::savePic($url, 'huxing'); 
                $upd2 = ['mpic'=>$res, 'aflag'=>'7'];
                db()->table('docs_huxing')->where("did='{$row['did']}'")->data($upd2)->update();
                $cnt++;
            } 
        }
        return $cnt;
    }

    static function fixAlbum(){
        $cnt = 0; 
        $list = db()->table('docs_album')->where("aflag=8")->limit(50)->select();
        if($list){ 
            foreach($list as $row) {
                $url = str_replace('h_170,w_220','h_600,w_800',$row['mpic']);
                $res = caiBase::savePic($url, $row['part']); 
                $upd2 = ['mpic'=>$res, 'aflag'=>'7'];
                db()->table('docs_album')->where("did='{$row['did']}'")->data($upd2)->update();
                $cnt++;
            } 
        }
        return $cnt;
    }

}

/*
 
*/
