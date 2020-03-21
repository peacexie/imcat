<?php
namespace imcat;

// extPano
class extPano{

    //var $xxx; 

    // 全景切图 krcut
    // 参数太长分批? 任务排队?
    static function krCut($tab, $make='umake', $exp=1800){
        // 设置执行超时时长
        @ini_set("max_execution_time", $exp);
        @ini_set("request_terminate_timeout", $exp); // 兼容php-fpm
        $op = KRPATH."/$make.".(IS_WIN?'bat':'sh');
        //die("$op $tab"); // "/xxx/umake.bat /dir/a.jpg /dir/b.jpg"
        $res = exec("$op $tab");
        return $res;
    }

    // 合并百度全景图, z=0-5, bdJoin
    static function bdJoin($sid, $z=3, $d='mapsv0'){
        $url0 = "https://$d.bdimg.com/?qt=pdata&sid=$sid&pos=0_0&z=$z";
        $x = pow(2, ($z-1));
        $a = $z>2 ? ($x/2)-1 : 0;
        $b = $z>1 ? ($x-1) : 0; echo "<b>z=$z: {$a}_{$b}</b><br>";
        $cw = [0=>100, 1=>512];
        $ch = [0=>75, 1=>256];
        $w = isset($cw[$z]) ? $cw[$z] : ($b+1) * 512; 
        $h = isset($ch[$z]) ? $ch[$z] : ($a+1) * 512;
        $arr = []; 
        $img = imagecreatetruecolor($w, $h); //新建一个真彩图像
        for($i=0; $i<=$a;$i++){ 
            for($j=0;$j<=$b;$j++){
                $k = "{$i}_{$j}"; echo "$k, ";
                $url = str_replace('0_0', $k, $url0);
                $data = comHttp::doGet($url);
                $itm = imagecreatefromstring($data);
                imagecopy($img, $itm, $j*512, $i*512, 0, 0, 512, 512);
                //$arr[$i][$j] = $data;
            } echo "<br>";
        } //dump($arr);
        return $img;
    } 

    // 
    function xxx(){ 
        //@exec("ifconfig -a", $this->return_array); 
        return $this->xxx; 
    } 

}

/*

--------------------------------- 

### 全景切图 demo

  # tab
    $rp = 'E:/dir/path';
    $tab = "$rp/quan-img1.jpg $rp/quan-img2.jpg";
    $res = extPano::krCut($tab);
    var_dump($res);
  # op
    win : umake.bat
    linux : umake.sh

--------------------------------- 

### 百度全景图分析

* demo
    $img = extPano::bdJoin($sid, $zoom);
    imagejpeg($img, "E:/Webs/pano/org-test/pano-baidu/$sid-z$zoom.jpg");

* 样例图分析
  - https://mapsv0.bdimg.com/?qt=pdata&sid=09006600121707221510490468P&pos=0_0&z=0
  - https://mapsv0.bdimg.com/?qt=pdata&sid=09006600121707221510490468P&pos=0_0&z=1
  - https://mapsv0.bdimg.com/?qt=pdata&sid=09006600121707221510490468P&pos=0_1&z=2 2^1=2
  - https://mapsv0.bdimg.com/?qt=pdata&sid=09006600121707221510490468P&pos=1_3&z=3 2^2=4
  - https://mapsv0.bdimg.com/?qt=pdata&sid=09006600121707221510490468P&pos=3_7&z=4 2^3=8
  - https://mapsv0.bdimg.com/?qt=pdata&sid=09006600121707221510490468P&pos=7_15&z=5 2^4=16
  -  128 + 32 + 8 + 2 + 2

* 域名和参数
  - 域名：mapsv0.bdimg.com 或 mapsv1.bdimg.com，这个应该是cdn域名
  - sid：场景id，打开任意全景图，从地址栏复制出来
  - pos图片切片位置，与z缩放级别同时使用，对应如下

* z缩放级别
  - 一共6个级别：从0-5
  - pos=0_0&z=0 缩略图
  - pos=0_0&z=1 最小的全景图
  - pos=0_1&z=2 由0_0, 0_1 共2幅 组成的全景图
  - pos=1_3&z=3 由0_0 ~ 1_3 共2x4幅 组成的全景图
  - pos=3_7&z=4 由0_0 ~ 3_7 共4x8幅 组成的全景图 
  - pos=7_15&z=5 由0_0 ~ 7_15 共8x16幅 组成的全景图

--------------------------------- 

*/
