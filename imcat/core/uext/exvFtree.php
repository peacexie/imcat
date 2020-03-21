<?php
namespace imcat;

// 
class exvFtree{

    // 预留属性
    static $part = 'demo'; // 分段 8,demo,test
    static $extab = 'exdemo'; // 分表 0,main,exdemo

    static function rowActs($fm, $kid, $act){
        $sex = basReq::val('sex');
        $mets = basReq::val('mates'); 
        $meta = empty($mets) ? [] : explode(',',$mets);
        $metb = basReq::arr('imets');
        $suns = basReq::arr('isuns');
        $newmts = $okid = '';
        if(!empty($metb)){ // mates>移除
            foreach($meta as $met){
                if(in_array($met,$metb)){
                    if($act=='edit' || $act=='addc'){
                        $up4['mates'] = '';
                        self::dbNow("mates='$kid'")->data($up4)->update();
                    }
                 }else{
                    $newmts .= ($newmts?',':'')."$met";
                 }
            }
        }else{
            $newmts = $mets;
        } 
        if(is_numeric($fm['name'])){
            $okid = $fm['name'];
            $orow = self::dbNow($okid);
            if(!$orow || $orow['kid']==$kid || $orow['part']!=self::$part){
                die("Error:`$okid`!");
            }
        }else{
            if(empty($fm['name'])){
                die("Null:`name`!");
            }
        }
        if(!$kid && empty($_SESSION['ftuser_parts'])){ // 超管(不指定区域)才可添加
            unset($fm['kid']);
            $fm['part'] = self::$part;
            $insid = self::dbNow("kid='$kid'")->data($fm)->insert();
            return $insid;
        }
        if($act=='edit'){
            // mates>移除
            if(!empty($metb)) $fm['mates'] = $newmts;
            // suns>移除
            if(!empty($suns)){
                foreach($suns as $sun){
                    $upd[$sex=='男'?'did':'mid'] = '';
                    self::dbNow("kid='$sun'")->data($upd)->update();
                }
            }
            self::dbNow("kid='$kid'")->data($fm)->update();
            $tops = basReq::arr('tops'); 
            if(!empty($tops)){ foreach($tops as $key=>$top) {
                self::dbNow("kid='$key'")->data(['top'=>$top])->update();
            } }
            return '-';
        }
        if($act=='addf' || $act=='addm'){
            if($okid){
                $insid = $okid;
            }else{
                unset($fm['did'],$fm['mid']); // 移除当前人父母资料
                $fm['sex'] = $act=='addf'?'男':'女';
                $fm['part'] = self::$part;
                $insid = self::dbNow()->data($fm)->insert();
            }
            $upd[$act=='addf'?'did':'mid'] = $insid; // 把新增人设置为当前人父/母
            self::dbNow("kid='$kid'")->data($upd)->update();
            return $insid;
        }
        if($act=='addb' || $act=='addg'){
            unset($fm['did'],$fm['mid']); // 移除当前人父母资料
            $fm[$sex=='男'?'did':'mid'] = $kid; // 把当前人设置为新增人的父/母
            $fm[$sex=='男'?'mid':'did'] = intval($mets); // 配偶设置成母/父(多个取第一个)
            if($okid){
                $insid = $okid;
                $upd = ['did'=>$fm['did'],'mid'=>$fm['mid']];
                self::dbNow("kid='$insid'")->data($upd)->update();
            }else{
                $fm['sex'] = $act=='addb' ? '男' : '女'; // 根据子女设置男/女
                $fm['part'] = self::$part;
                $insid = self::dbNow()->data($fm)->insert();
            }
            return $insid;
        }
        if($act=='addc'){
            if($okid){
                $insid = $okid;
                $upd['mates'] = $orow['mates'] ? "{$orow['mates']},$kid" : $kid;
                self::dbNow("kid='$insid'")->data($upd)->update();
                $mul = strpos($upd['mates'],',') ? 1 : 0;
            }else{
                unset($fm['did'],$fm['mid']); // 移除当前人父母资料
                $fm['sex'] = $sex=='男' ? '女' : '男';
                $fm['part'] = self::$part;
                $fm['mates'] = $kid; // 把当前人设置为新增人的配偶
                $insid = self::dbNow()->data($fm)->insert();
                $mul = 0;
            }
            // 重新设置当前人配偶资料
            $upd['mates'] = $newmts.(($newmts?',':'')."$insid");
            self::dbNow("kid='$kid'")->data($upd)->update();
            // 更新suns
            if(!$mul && !strpos($upd['mates'],',')){
                $k1 = $sex=='男' ? 'did' : 'mid';
                $kc = $sex=='男' ? 'mid' : 'did';
                $upd2[$kc] = $insid;
                self::dbNow("$k1='$kid' AND $kc='0'")->data($upd2)->update();
                $upd3[$k1] = $kid;
                self::dbNow("$kc='$insid' AND $k1='0'")->data($upd3)->update();
            }
        }
        return '-';
    }

    // 列表/搜索
    static function getList($ids=''){
        $whr = "(did>0 OR mid>0)"; // AND mates>0
        $kw = req('kw'); $eid = req('eid');
        if($ids){
            $whr = "kid IN($ids)";
        }elseif($kw && is_numeric($kw)){
            $whr = "kid='$kw' OR did='$kw' OR mid='$kw' OR mates='$kw'";
        }elseif($kw){
            $whr = "name LIKE '$kw%'";
        }
        if($eid) $whr .= " OR kid='$eid'";
        $res = self::dbNow("$whr")->order('kid DESC')->limit(10)->select();
        //echo db()->getSql();
        if(!empty($res)){ foreach($res as $rk=>$row){
            $res[$rk]['pr0'] = self::getPair($row);
        } }
        return $res;
    }

    // 仅自己的子女
    static function getSuno($row){ 
        $whr = $row['sex']=='男' ? "did='{$row['kid']}'" : "mid='{$row['kid']}'";
        $res = self::dbNow($whr)->order('top,kid')->select();
        return $res;
    }
    // 同辈(兄弟姐妹)/子辈/孙辈
    static function getAbcs($row, $view=0){
        if($view=='p1'){
            $whr = "kid='{$row['kid']}'";
        }elseif($row['did'] && $row['mid']){
            $whr = "did='{$row['did']}' OR mid='{$row['mid']}'";
        }elseif($row['did'] || $row['mid']){
            $whr = $row['did'] ? "did='{$row['did']}'" : "mid='{$row['mid']}'";
        }else{
            $whr = 0;
        }
        $res = $whr ? self::dbNow("$whr")->order('top,kid')->select() : [$row];
        self::getSuns($res);
        return $res;
    }
    // 自己和配偶的子女
    static function getSuns(&$data, $deep=1){
        $dk = "suns$deep";
        if(!empty($data)){
            foreach ($data as $sk=>$srow) {
                $fk1 = $srow['sex']=='男' ? 'did' : 'mid'; $fk2 = $fk1=='did' ? 'mid' : 'did';
                $and = empty($srow['mate']) ? '' : " AND $fk2 IN('".str_replace(",","','","{$srow['mate']}")."'')";
                $suns = self::dbNow("$fk1='{$srow['kid']}' $and")->order('top,kid')->select();
                $data[$sk][$dk] = $suns;
                if($deep<2){
                    self::getSuns($data[$sk][$dk],$deep+1);
                }
            }
        }
    }
    // 父母
    static function getPair($row, $multi=0){
        $res['did'] = empty($row['did']) ? [] : self::dbNow($row['did']);
        $res['mid'] = empty($row['mid']) ? [] : self::dbNow($row['mid']);
        if($multi){
            $ids = "~,";
            $ids .= empty($row['did']) ? '' : $row['did'].',';
            $ids .= empty($row['mid']) ? '' : $row['mid'].',';
            $tab = ['did'=>'mid', 'mid'=>'did'];
            foreach($tab as $k1=>$k2){
                if(empty($res[$k1]['mates'])) continue;
                $ka = explode(',',$res[$k1]['mates']);
                foreach($ka as $k3) {
                    if($k3 && !strpos($ids,",$k3,")){
                        $res[$k2]['_cps'][] = self::dbNow($k3);
                        $ids = "$k3,";
                    }
                }
            }
        }
        return $res;
    }

    static function dbNow($wk=''){
        $db = db()->table('ftree_'.self::$extab);
        if(!$wk) return $db;
        $whrp = "part='".self::$part."'";
        if(is_numeric($wk)) return $db->where("$whrp AND (kid='$wk')")->find();
        return $db->where("$whrp AND ($wk)");
    }

}
