<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

//echo $mkv;

/* act=list,up,del,view,imp */
$act = req('act','list'); 
$vfp = req('vfp'); if($vfp && strpos($vfp,'/')){ die('[del] Error xls File!'); }
$itmid = req('itmid'); 

#$pt_ids = [];
$base = DIR_VARS . "/gbdown"; //echo $base;excel
include __DIR__.'/gb-func.php';
include __DIR__.'/imp-nav.php';


if ($act == 'del'){
    if(strlen($vfp)<10){ die('Error!'); }
    @unlink("$base/".$vfp);
    $act = 'list';
}

if ($act == 'up'){
    if(!empty($_POST['upLoad'])){ // LINKOK,FAILED
      foreach($_FILES as $f){
        if($f['name']){ 
            $ext = strtolower(strchr($f['name'], '.'));
            if($ext!='.xls'){
                die('[up] Error xls File!');
            }
            $fp = "$base/".date('Y-md-Hi').$ext; //$f['name']
            $r = move_uploaded_file($f['tmp_name'],$fp); 
            chmod($fp, 0755);//设定上传的文件的属性 
            echo "<li>上传OK! "; 
            $act = 'list';
      } }
    }else{
    //include './acts-incs.php';
    ?>
    <form id="fmup" name="fmup" method="post" action="?<?=$mkv?>&act=up" enctype="multipart/form-data">
        <ul class="">
            <li>上传文件：</li>
            <li><input type="file" name="fileup1" id="fileup1"></li>
            <li><input type="submit" name="upLoad" id="upLoad" value="上传" /></li>
        </ul>
    </form>
    <?php
    }
}

if ($act == 'list'){
    $arr = glob("$base/*.xls"); 
    echo "<ul>\n";
    foreach ($arr as $xls) { $vfp = str_replace([DIR_VARS,'/gbdown/'],'',$xls);
        echo "<li><a href='?$mkv&act=view&vfp=$vfp'>$vfp</a> # <a href='?$mkv&act=del&vfp=$vfp'>删除</a></li>\n";
    }
    echo "</ul>\n";
}

if($vfp){
    $fp = "$base/".$vfp;
    $data = extExcel::exRead($fp); //Excle_exRead($fp); //dump($data);
    $parts = ['info'=>'主体参数', 'parts'=>'配件参数', 'servers'=>'服务参数'];
    $itms = xlsToarr($data['cells'], $parts); //dump($itms);
}

if ($act == 'view'){
    echo "<ul>\n";
    foreach ($itms as $no => $itm) {
        echo "<li>[<a href='?$mkv&act=view&vfp=$vfp&itmid=$no'>查看</a>] {$itm['name']}</li>\n";
    }
    echo "</ul>\n";
    if($itmid){
        $itm = $itms[$itmid]; //dump($itm);
        $pinmu = $itm['主体参数']['0']['采购品目'];
        // 
        echo "\n<h3>{$itm['name']}</h3>\n";
        echo "<ul>\n";
        /*foreach ($itm['配件参数'] as $pk => $pv) { 
            //$name = $pv['编号'].'/'.$pv['配件名称'];
            echo "\n<li>配件: $pv[编号]: ".opts_type(100+$pk, $pv['配件名称'])."</li>\n";
        }*/
        echo "\n<li style='display:none'><a href='?$mkv&act=imp&' id='imp_link' urp='vfp=$vfp&itmid=$itmid'>导入</a></li>\n";
        echo "\n<li>主体产品：".opts_type('type',$pinmu)."</li>\n";
        echo "</ul>\n";
        // 
        $str = arrTotab($itm, $parts, $itm['name']); 
        echo $str;
    }
    #dump($pt_ids); // 配件s
}

if($act=='check' || $act=='imp'){
    $itm = $itms[$itmid]; //dump($itm);
    /*foreach ($itm as $ik => $iv) {
        $ik = trim(preg_replace("/\s+/i", ' ', $ik));
        $ik = str_replace(['（','）'], ['(',')'], $ik);
        $itm[$ik] = $iv;
    }*/
    $key = req('key'); 
    $modid = req('modid');
    if($key=='type'){
        $k1 = '主体参数';
        $k2 = 0;
        $snote = _getFpfmt_($itm[$k1][$k2]['商品型号'], $itm['name'], 1); //'重名标记:'.
    }else{
        $k1 = '配件参数';
        $k2 = $key-100;
        $snote = 'ACCODE:'.$itm[$k1][$k2]['编号']; 
        #if($act=='imp'){ die('.end.'); }
    }
    echo "\n<b>省采标准属性</b>\n";
    dump($itm[$k1][$k2]);
    // 
    $ctab = $itm[$k1][$k2];
    $res = db()->table('exd_uatt')->where("pid='$modid'")->order('top')->select();
    $atab = []; // dump($ctab); dump($res);
}

if ($act == 'check'){
    $aord = []; // 保存顺序
    foreach ($res as $rk => $rv) { 
        unset($ctab[$rv['title']]);
        $atab[$rv['title']] = $rv['kid'];
        $aord[$rv['title']] = $rk;
    }
    echo "\n<b>系统类型配置：</b><br>\n";
    dump($atab); //dump($aord);
    echo "\n<b>=== 比较结果 === ：</b><br>\n";
    if(empty($ctab)){
        echo "<br>\n -- 属性项相同 ::: <a href='?$mkv&act=check&vfp=$vfp&itmid=$itmid&key=$key&modid=$modid&ord=1'>调整顺序</a> <br>\n";
        $ord = req('ord'); // dump($aord);
        if($ord){ 
            $no=0;
            foreach ($itm[$k1][$k2] as $ck => $cv) {
                $top = 10 + $no;
                $irow = ['top'=>$top]; //dump($irow); dump("pid='$modid' AND title='$ck'");
                db()->table('exd_uatt')->data($irow)->where("pid='$modid' AND title='$ck'")->update();
                $no++;
            }
        }
    }else{
        echo "<br>\n -- 如下属性项`缺失` ::: <a href='?$mkv&act=check&vfp=$vfp&itmid=$itmid&key=$key&modid=$modid&imp=1'>导入</a> <br>\n";
        dump($ctab);
        $imp = req('imp'); 
        if($imp){ // attr_input_type='0',attr_type
            $dbkid = admPFunc::umodKid('exd_uatt', '10240'); $no=0; 
            foreach ($ctab as $ck => $cv) {
                $ikid = $dbkid + $no +1; //$top = 10 + $no;
                $irow = ['kid'=>$ikid, 'title'=>$ck, 'pid'=>$modid, 'type'=>'input', 'top'=>66]; //dump($irow);
                db()->table('exd_uatt')->data($irow)->insert();
                $no++;
            }
        }
    }
    #dump($res);
}

/*

*/

if ($act == 'imp'){

    // ------ 主体产品
    $res = db()->table('docs_cargo')->field('did,catid,xinghao,title,attmod,apino')->where("apino='$snote'")->find(); 
    if($res){
        $insid = $res['did'];
        echo "<br>\n -- 已经含有此产品<br>\n";
        dump($res);
    }else{
        echo "<br>\n -- 导入商品<br>\n";
        $name = $key=='type' ? $itm['name'] : $itm[$k1][$k2]['编号'].':'.$itm[$k1][$k2]['配件名称'];
        $dbkar = glbDBExt::dbAutID('docs_cargo');
        $insid = $dbkar[0];
        $irow = ['did'=>$dbkar[0], 'dno'=>$dbkar[1]] + [
            'title'=>str_replace(["'"], ['`'], $name), 'attmod'=>$modid, 'apino'=>$snote, 'catid'=>'0', 
            'hinfo'=>'', 'mpic'=>'', 'color'=>'', 'guige'=>'', 'xinghao'=>'', 'diggtop'=>'0', 'diggdown'=>'0'
        ];
        db()->table('docs_cargo')->data($irow)->insert();
        dump($irow);
    } //die();

    // ------ 服务参数
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
    } dump($menu); 
    // ------ 属性操作
    echo "<br>\n -- 属性操作<br>\n";
    $attcom = "";
    foreach ($itm[$k1][$k2]as $pk => $pv) { 
        $attcom .= "$pk=`$pv`\n";
    } dump($attcom); 
    // ------ update
    $irow = ['adrem'=>$adrem, 'menu'=>$menu, 'attcom'=>$attcom, ]; //dump($irow);
    $res = db()->table('dext_cargo')->where("did='$insid'")->find(); 
    if($res){
        db()->table('dext_cargo')->data($irow)->where("did='$insid'")->update(0);
    }else{
        $iext = $irow + ['did'=>$insid, 'author'=>'', 'source'=>'', 'seo_key'=>'', 'seo_des'=>'', 'seo_tag'=>'', 'pbat'=>''];
        db()->table('dext_cargo')->data($iext)->where("did='$insid'")->insert(0);
    }
    dump("did='$insid'");
    //seller_note
    die('.end.');
}

?>