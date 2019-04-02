<?php
namespace imcat;

// ...类 caiBase
class caiBase{    

    // 分目录保存一个图片,如果有这个图片,就略过
    static function savePic($url, $pdir='house'){
        // 
        // //pic.ha15bb.jpg?x-oss-process=image/resize,m_fill,h_170,w_220
        if(substr($url,0,2)=='//') $url = "http:$url";
        if(strpos($url,'?')){
            $bnm = basename(substr($url,0,strpos($url,'?')));
        }else{
            $bnm = basename($url);
        }
        $id = abs(crc32($bnm));
        $sdir = 10000 + ($id % 720); // 分720个目录
        $file = DIR_URES."/mdown/$pdir/$sdir/$id-".md5($url).strrchr($bnm,'.');
        if(!file_exists($file)){
            $tab = ['mdown', $pdir, $sdir];
            $bdir = DIR_URES."/";
            foreach ($tab as $dkey) {
                if(!is_dir("$bdir$dkey/")){
                    mkdir("$bdir$dkey/");
                }
                $bdir .= "$dkey/";
            }
            comHttp::downSave($url, $file, 1);
        }
        return '{uresroot}'.substr($file,strlen(DIR_URES)); 
    }

    // xxxx
    static function dbKid($tab,$no=0,$cnt=0,$fix=''){
        if(!$cnt){
            $kid = substr(basKeyid::kidTemp('md2'),0,10).$fix; //2019-3t-d7(7v)
            $kid .= $no<10 ? "0$no" : $no;
        }else{ //if($cnt<3){
            $kid = substr(basKeyid::kidTemp('md2'),0,9).$fix;
            $kid .= basKeyid::kidRand('a',3);
            if($cnt>3) $kid .= basKeyid::kidRand('a',1);
        }
        $old = db()->table($tab)->where("did='{$kid}'")->find();
        if(!$old){
            return $kid;
        }else{
            $cnt++;
            return self::dbKid($tab,$no,$cnt);
        }
    }

    // 得到属性数组
    static function attrs($key=''){
        $data = read($key);
        $res = [];
        foreach($data as $kid=>$row) {
            $pid = $row['pid'];
            if($pid=='0'){
                $res['0'][$kid] = $row['title'];
            }else{
                $res[$pid][$kid] = $row['title'];
            }
        }
        return $res;
    }
    // 由属性val找到对应的属性key
    static function atkey1($arr,$val,$cut=0){
        if(!$val) return '';
        if($cut) $val = mb_substr($val,0,$cut);
        foreach($arr as $kid=>$kv) {
            if($cut) $kv = mb_substr($kv,0,$cut);
            if($kv==$val){
                return $kid;
            }
        }
        return '';
    }
    // 由属性vals找到对应的属性keys
    static function atkeys($arr,$vals,$cut=0){
        if(!$vals) return '';
        $vala = explode(',',$vals);
        $keys = '';
        foreach ($vala as $val) {
            if(!$val) continue;
            if($cut) $val = mb_substr($val,0,$cut);
            foreach($arr as $kid=>$kv) {
                if($cut) $kv = mb_substr($kv,0,$cut);
                if($kv==$val){
                    $keys .= ($keys?',':'').$kid;
                }
            }
        }
        return $keys;
    }

}
