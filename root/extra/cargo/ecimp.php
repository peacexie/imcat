<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

//echo $mkv;

/* act=list,up,del,view,imp */
$act = req('act','cfgs'); 
$cat_id = req('cat_id'); 
$itmid = req('itmid'); 

#$pt_ids = [];
$base = DIR_VARS . "/ecshop"; //echo $base;excel
include __DIR__.'/ec-func.php';
include __DIR__.'/imp-nav.php';


// cfgs
include DIR_ROOT.'/cfgs/boot/cfg_db.php'; //dump($_cfgs); 
$eccfg = read('outdb.ecshop','ex');
$dbcfg = array_merge($_cfgs,$eccfg);
if($act=='cfgs'){ 
    echo "配置位置：/root/cfgs/excfg/ex_outdb.php；<br>\n";
    echo "配置节点：_ex_outdb.ecshop；<br>\n";
    $re3 = devRun::runMydb3($dbcfg); 
    echo "检查结果：".$re3['mysqli']['res'].' '.$re3['mysqli']['info']."；<br>\n";
    dump($re3['mysqli']); dump($re3);
    //dump($eccfg);
}else{
    $db = db();
    $ec = db($dbcfg);
}

if($act=='test'){
    // ($tab='utest_keyid', $tmp='max', $n=0, $xTime='')
    $kar = glbDBExt::dbAutID('docs_cargo', 'max', 0, '2019-12-23 12:30:10'); dump($kar);
    $kar = glbDBExt::dbAutID('docs_cargo', 'max', 0, '2019-12-23 12:30:30'); dump($kar);
    $kar = glbDBExt::dbAutID('docs_cargo', 'max', 0, '2019-12-23 12:30:50'); dump($kar);
    $kar = glbDBExt::dbAutID('docs_cargo', 'max', 0, '2019-12-23 12:31:10'); dump($kar);   
}

/*
    <a href="?<?=$mkv?>&act=immod">商品类型</a> - 
    <a href="?<?=$mkv?>&act=imcat">商品栏目</a> - 
    <a href="?<?=$mkv?>&act=impro">导入产品</a> - 
    <a href="?<?=$mkv?>&act=impic">导入图片</a> -
    <a href="?<?=$mkv?>&act=impart">导入配件</a>
*/


if($act=='immod'){
    
    if($cat_id){
        $lista = $ec->table('attribute')->where("cat_id='$cat_id'")->select(); // ->limit(3)
        foreach($lista as $rk=>$rv) {
            echo "$rv[attr_input_type] : $rv[attr_id] : $rv[attr_name] --- $rv[attr_values]<br>\n";
        }
        dump($rv); die();
    }

    # 根据情况导入，这里假如都导入到`5015`这个大分组下
    $pid0 = '5015'; //Test模型组 // 'exd_uatt' : 'exd_umod'; '10240' : '5120';
    $mid0 = admPFunc::umodKid('exd_umod', '5120');
    $att0 = admPFunc::umodKid('exd_uatt', '10240');
    $listm = $ec->table('goods_type')->order('cat_id')->select(); // ->limit("2,1")
    $no1 = $no2 = 1; $mcfg = [];
    foreach($listm as $rk=>$rv){
        $cat_old = $rv['cat_id'];
        $name1 = trim($rv['cat_name']);
        $mdid = $mid0 + $no1;
        $row = ['kid'=>$mdid, 'title'=>$name1, 'pid'=>$pid0, 'top'=>66]; //dump($row);
        $whr = "pid='$pid0' AND title='$name1'"; //echo $whr;
        if($irow=$db->table('exd_umod')->where($whr)->find()){ 
            echo "~SKIP";
            $mdid = $irow['kid'];
        }else{
            echo "DBINS";
            #$db->table('exd_umod')->data($row)->insert();
            $no1++;
        }
        echo " : <b>$mdid-$name1-[$cat_old]</b> ";
        echo "<a href='?$mkv&act=immod&cat_id=$cat_old'>attribute</a> <br>\n";
        $mcfg = ['id'=>$cat_old, 'mod'=>$mdid, 'name'=>$name1];

        // attr
        $atrows = $ec->table('attribute')->where("cat_id='$cat_old'")->select();
        $atts = [];
        foreach($atrows as $mk=>$mv){
            $att_old = $mv['attr_id'];
            $name2 = trim($mv['attr_name']);
            $atid = $att0 + $no2;
            $top = $mv['sort_order'];
            $text = basElm::linestrim($mv['attr_values'], 0);

            $text = preg_replace_callback("/\s*([\n|\r])+\s*/i", function($itms){
                return "\n"; // 去每行两边空白
            }, trim($mv['attr_values']));
            $type = $text ? 'select' : 'input';
            if($text && $mv['attr_type']=='2'){ $type='cbox'; } 
            $row = ['kid'=>$atid, 'title'=>$name2, 'pid'=>$mdid, 'type'=>$type, 'so'=>$mv['attr_index'],'top'=>$top, 'cfgs'=>$text];
            $whr = "pid='$mdid' AND title='$name2'"; //echo $whr;
            if($jrow=$db->table('exd_uatt')->where($whr)->find()){ 
                echo " &nbsp; - ~SKIP";
                $atid = $jrow['kid'];
            }else{
                echo " &nbsp; - DBINS";
                #$db->table('exd_uatt')->data($row)->insert();
                $no2++;
            }
            echo " : $atid-$name2-$type [$att_old] ~~~ $text<br>\n"; //dump($text); dump($mv['attr_values']);
            $atts[$att_old] = ['name'=>$name2, 'isso'=>$mv['attr_index']];
        }//*/
        $mcfg['atts'] = $atts;
        file_put_contents("$base/mod_".$mcfg['id'].".htm", json_encode($mcfg,JSON_UNESCAPED_UNICODE));
    }
}


if($act=='imcat'){
    $pre = 'c'; $deep = 0; $no=5000; // glbDBExt::dbNxtID($tabid,$mod,$pid); // c[123]5012
    $listc = $ec->table('category')->order('parent_id,sort_order')->select();
    $no1 = $no2 = 1; $ctab = [];
    foreach($listc as $rk=>$rv){
        $idc = $rv['cat_id'];
        $namec = $rv['cat_name'];
        $pid0 = $rv['parent_id'];
        $kidc = "$pre$deep$no";
        $pidc = $pid0 ? (isset($ctab[$pid0]) ? $ctab[$pid0]['kid'] : '0') : '0';
        $row = ['kid'=>$kidc, 'model'=>'cargo', 'title'=>$namec, 'pid'=>$pidc, 'enable'=>1, 'top'=>$rv['sort_order']]; //dump($row);
        $whr = "pid='$pidc' AND title='$namec'"; //echo $whr;
        if($irow=$db->table('base_catalog')->where($whr)->find()){ 
            echo "~SKIP";
            $kidc = $irow['kid'];
        }else{
            echo "DBINS";
            #$db->table('base_catalog')->data($row)->insert();
            $no++;
        }
        echo " : <b>$idc-$namec-[$kidc-$pidc]</b> <br>\n";
        $ctab[$idc] = ['id'=>$idc, 'kid'=>$kidc, 'name'=>$namec];
    }
    file_put_contents("$base/cats_all.htm", json_encode($ctab,JSON_UNESCAPED_UNICODE));
}


if ($act=='impro'){

    $max = 2; //500;
    $rows = $ec->table('goods')->where("imflag='0' AND goods_type>'0'")->order('goods_id')->limit($max)->select();
    foreach($rows as $rv){
        // values
        $goods_id = $rv['goods_id']; $goods_name = $rv['goods_name'];
        $cat_id = $rv['cat_id']; $goods_type = $rv['goods_type'];
        $last = empty($rv['add_time']) ? $rv['last_update'] : $rv['add_time'];
        $kar = glbDBExt::dbAutID('docs_cargo', 'max', 0, $last); //dump($kar);
        $mpic = $rv['goods_thumb']; // 复制,路径处理...
        $click = $rv['click_count'];
        $brand_id = $rv['brand_id']; // 怎样对应,根据需要处理
        $keywords = $rv['keywords']; $goods_brief = $rv['goods_brief']; $goods_desc = in($rv['goods_desc']);
        $price = $rv['mall_price']; //goods_weight    market_price    shop_price  mall_price
        // umod,uatt 
        $modid = '0'; $attso = $attcom = '';
        if($goods_type){
            $fdata = comFiles::get("$base/mod_$goods_type.htm");
            $farr = json_decode($fdata,1); //dump($farr);
            $modid = isset($farr['mod']) ? $farr['mod'] : '0';
        }
        $atts = $ec->table('goods_attr')->where("goods_id='$goods_id'")->select();
        foreach($atts as $av) {
            $atid = $av['attr_id'];
            $atval = trim($av['attr_value']);
            if(isset($farr['atts'][$atid]) && !empty($farr['atts'][$atid]['isso'])){
                $attso .= "{$farr['atts'][$atid]['name']}=`$atval`\n";
            }else{
                $attcom .= "($atid)=`$atval`\n";
            }
        }
        // insert (细节自己完善添加)
        $rowb = ['did'=>$kar[0], 'dno'=>$kar[1]] + [
            'title'=>str_replace(["'"], ['`'],$goods_name), 'attmod'=>$modid, 'apino'=>'', 'catid'=>'0', 
            'hinfo'=>'', 'mpic'=>$mpic, 'color'=>'', 'guige'=>'', 'xinghao'=>'', 'diggtop'=>'0', 'diggdown'=>'0',
            'attso'=>$attso, 'aip'=>"($goods_id)", 
        ];
        #$db->table('docs_cargo')->data($rowb)->insert();
        #dump($rowb);
        #$rtmp = ['adrem'=>$adrem, 'menu'=>$menu, 'attcom'=>$attcom, ];
        $rowd = [
            'did'=>$kar[0], 'author'=>'', 'source'=>'', 'seo_key'=>$keywords, 'seo_des'=>$goods_brief, 'seo_tag'=>'', 
            'pbat'=>'', 'detail'=>$goods_desc, 'attcom'=>$attcom, 
        ];
        #$db->table('dext_cargo')->data($rowd)->where("did='$kar[0]'")->replace(0);
        #dump($rowd);
        #$ec->table('goods')->where("goods_id='$goods_id'")->data(['imflag'=>1])->update(0);
        echo "-- 导入商品 : $goods_id/$kar[0] ::: $goods_name # $attso # $attcom<br>\n";
    }

}


// 导入图片
if($act=='impic'){
    // 
}


// 导入配件
if($act=='impart'){
    // 
}


dump(basDebug::runInfo());
die('...');


/*
    1. ecs_goods_type, ecs_attribute : umod
    2. ecs_goods, ecs_goods_attr : cargo
    3. ecs_goods_gallery
    4. ecs_group_goods
    5. ecs_area, 
    ------------------------------
    表                  数据长度?    索引长度?   数据空闲?   自动增量?     行数?
    ecs_area            109,116     36,864      0           -           3,418
    ecs_attribute       33,144      23,552      0           1,739       842
    ecs_goods           40,295,428  1,925,120   0           1,000,349   15,811
    ecs_goods_attr      4,051,028   3,999,744   0           938,648     111,181
    ecs_goods_gallery   7,340,664   626,688     0           1,000,415   27,472
    ecs_goods_type      3,536       4,096       0           263         115
    ecs_group_goods     559         2,048       0           -           43
    ecs_region          69,656      112,640     0           3,409       3,408
    ecs_shipping_area   1,300       3,072       0           6            5  
*/


    #die();

    /*/ ------- 服务参数

    $adrem = "";
    foreach ($itm['服务参数']['0'] as $pk => $pv) { 
        $adrem .= "$pk=`$pv`\n";
    } //dump($adrem); 

    // ------ 配件参数
    { // if($key=='type')
        $menu = "主机x1, ";
        echo "<br>\n -- 标准配件列表\n<ul>\n";
        $upkar = glbDBExt::dbAutID('exd_upart'); 
        foreach ($itm['配件参数'] as $pk => $pv) { 
            $isnote = '编号:'.$pv['编号'];
            echo "\n<li>标准配件 # $isnote # {$pv['配件名称']}\n";
            $menu .= "{$pv['配件名称']}x1".($pk==count($itm['配件参数'])-1?'':', ');
            // exd_upart
            $res = db()->table('exd_upart')->where("pid='$insid' AND title='{$pv['配件名称']}'")->find(); 
            if($res){
                echo "已经存在！";
            }else{
                echo "新增配件！"; // kid    pid did title   guige   price   top cnt attcom
                $attcom = "";
                foreach ($itm['配件参数'][$pk]as $ipk => $ipv) { 
                    $attcom .= "$ipk=`$ipv`\n";
                } //dump($attcom); 
                $iext = ['kid'=>substr($upkar[0],0,11).($pk), 'pid'=>$insid, 'did'=>'0', 'title'=>$pv['配件名称'], 'guige'=>'', 
                    'top'=>(10+$pk), 'cnt'=>'1', 'attcom'=>$attcom];
                db()->table('exd_upart')->data($iext)->where("did='$insid'")->insert(0);
            }
            echo "</li>\n";
        }
        echo "</ul>\n";
    } dump($menu); */



?>