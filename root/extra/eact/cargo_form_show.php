<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init'); 
 
if(!empty($did)){
    $fmo = $db->table($dop->tbid)->where("did='$did'")->find();
    if(empty($fmo)){ die("Error:$did"); }
    $fme = $dop->tbext ? $db->table($dop->tbext)->where("did='$did'")->find() : [];
    $fme && $fmo = $fmo + $fme;
    $isadd = 0;
}else{
    $fmo = array();
    $isadd = 1;
}
$dop->fmo = $fmo;


$modcfgs = glbConfig::read($mod); 
$mfields = $modcfgs['f']; 

$fsale = ['ndb_sale','isacc','price','stock','weight','pbat','menu','serv',
    'ordcnt','click','diggtop','diggdown','apino','adrem'];
$fpics = ['mpic','detail','rel_pic'];
$frels = ['epart','rel_pro','rel_inf'];
$fhid = ['attmod','brand','attso','attcom','attdel'];
$fskips = array_merge($fsale, $fpics, $frels, $fhid);

$fdef = array_merge($fhid,['catid']);
foreach ($fdef as $dn => $dk) {
    if(!isset($fmo[$dk])){ $fmo[$dk] = ''; }
}


# form-head
$fmact = basReq::getURep($aurl[1],'recbk');
echo "<form id='fmlist' name='fmlist' method='post' action='$fmact' target=''>\n";
$recbk = basReq::val('recbk','');
$recbk = $recbk==='ref' ? basEnv::serval("HTTP_REFERER") : $recbk;
echo "<input name='recbk' type='hidden' value='$recbk' />\n";

echo "
<ul class='nav nav-tabs'>
  <li class='active'><a href='#sec_base' data-toggle='tab'>基础信息</a></li>
  <li ><a href='#sec_sale' data-toggle='tab'>销售信息</a></li>
  <li ><a href='#sec_tpic' data-toggle='tab'>图文描述</a></li>
  <li ><a href='#sec_attr' data-toggle='tab'>属性参数</a></li>
  <li ><a href='#sec_rels' data-toggle='tab'>配件关联</a></li>
</ul>
<div class='tab-content pv10'>
    <div class='tab-pane fade in active' id='sec_base'>\n";
        echo "<div class='table-responsive'>\n";
        echo "<table border='1' class='table tbdata'>\n";
        // 基础信息-start
        $skip = $fskips;
        foreach($mfields as $k=>$v){
            if(in_array($k, $skip)){ continue; }
            if($v['type']=='parts'){ //(分fskips字段...)
                echo "<tr><th>$v[title]</th><th fskipscfskipsass='tr'>$v[vtip]</th></tr>\n";
                continue;
            }else{
                $item = fldView::fitem($k, $v, $fmo);
                $item = fldView::fnext($mfields, $k, $fmo, $item, $skip);
                glbHtml::fmae_row($v['title'], $item);
            }
        }
        // 固定信息
        dopFunc::fmSafe();
        echo "<tr><th nowrap class='tc'>固定信息</th><th class='tc'>---</th></tr>\n";
        $show = '显示:'.$dop->fmShow();
        glbHtml::fmae_row(basLang::show('flow.title_attrtitle'),' &nbsp; ID:'.$dop->fmSetID()." &nbsp; $show");
        $dop->fmAE3();
        // 基础信息-end
        echo "</table></div>";
        echo "
    </div>
    <div class='tab-pane' id='sec_sale'>\n";
        echo "<div class='table-responsive'>\n";
        echo "<table border='1' class='table tbdata'>\n";
        // 图文描述-start
        $skip = ['ndb_sale'];
        foreach($mfields as $k=>$v){
            if(in_array($k, $skip)){ continue; }
            if(!in_array($k, $fsale)){ continue; }
            $item = fldView::fitem($k, $v, $fmo);
            $item = fldView::fnext($mfields, $k, $fmo, $item, $skip);
            glbHtml::fmae_row($v['title'], $item);
        }
        // 图文描述-end
        echo "</table></div>";
        echo "
    </div>
    <div class='tab-pane' id='sec_tpic'>\n";
        echo "<div class='table-responsive'>\n";
        echo "<table border='1' class='table tbdata'>\n";
        // 图文描述-start
        $skip = [];
        foreach($mfields as $k=>$v){
            if(!in_array($k, $fpics)){ continue; }
            $item = fldView::fitem($k, $v, $fmo);
            glbHtml::fmae_row($v['title'], $item);
        }
        // 图文描述-end
        echo "</table></div>";
        echo "
    </div>
    <div class='tab-pane' id='sec_attr'>\n";
        echo "<div class='table-responsive'>\n";
        echo "<table border='1' class='table tbdata'>\n"; 
        // 属性参数-start
        echo basJscss::imp("/~base/jslib/jstypes.js");
        #$fmo['attmod'] = '5012'; // 5122,a1047
        $attmodstr = fldEdit::layTypes('umod', 'attmod', $fmo['attmod']);
        glbHtml::fmae_row('模型',"$attmodstr");
        $catidstr = fldEdit::layTypes('cargo', 'catid', $fmo['catid']);
        glbHtml::fmae_row('栏目',"$catidstr");
        $brandstr = fldEdit::layTypes('brand', 'brand', $fmo['brand']);
        glbHtml::fmae_row('品牌',"$brandstr");
        // 
        foreach($mfields as $k=>$v){
            if(!in_array($k,$fhid)){ continue; }
            if(in_array($k,['attmod','brand'])){ continue; }
            if($k=='attso'){
                $tab_com = "<input id='tab_com' type='hidden' value='' />";
                $tab_so = "<input id='tab_so' type='hidden' value='' />";
                $tab_msg = "<th class='tc'>{$tab_com}{$tab_so}选择模型 设置属性</th>";
                $atttable = "<table id='umod_atts' class='table tbdata'><tr><td colspan=2 class='tc'>(...)</td></tr></table>";
                echo "<tr><th nowrap class='tc'>属性设置</th>$tab_msg</tr>\n";
                echo "<tr><td colspan=2 class='tc'>$atttable</td></tr>\n";
            }
            $item = fldView::fitem($k, $v, $fmo);
            $item = str_replace("rows='15'", "rows='4'", $item); 
            #$item = str_replace("style='", "style='display:none;", $item); 
            $item = str_replace("style='", "readonly style='background:#DDD;", $item); 
            glbHtml::fmae_row($v['title'], $item);
        }
        $att_vals = extCargo::fieldAtts($fmo);
        //dump($att_vals); dump($fmo['attso']); dump($fmo['attcom']);
        $jscfgs = "var att_vals=".comParse::jsonEncode($att_vals).";\n";
        echo basJscss::jscode("{$jscfgs}");
        // 属性参数-end
        echo "</table></div>";
        echo "
    </div>
    <div class='tab-pane' id='sec_rels'>\n";
        echo "<div class='table-responsive'>\n";
        echo "<table border='1' class='table tbdata'>\n";
        #glbHtml::fmae_row('配件信息',$html);
        echo "<tr><td class='tc'>配件信息</td><td class='tl'>\n";
        $fparts = DIR_ROOT.'/extra/cargo/parts-ins.php';
        include $fparts;
        echo "</td></tr>\n";
        // 配件关联-start
        $skip = [];
        foreach($mfields as $k=>$v){
            if(!in_array($k, $frels)){ continue; }
            $item = fldView::fitem($k, $v, $fmo);
            glbHtml::fmae_row($v['title'], $item);
        }
        // 属性参数-end
        echo "</table></div>";
        echo basJscss::imp("/~tpl/cargo-atts.js");
        echo "
    </div>
</div>";

# bsend
$input = "<input name='bsend' type='submit' class='btn' value='保存商品' />";
echo "<div class='tc'>$input</div>";

# form-end ["mod|$mod","isadd|$isadd"]
echo "<input name='mod' type='hidden' value='$mod' />\n"; 
echo "<input name='isadd' type='hidden' value='$isadd' />\n"; 
echo "</form>\n";


// 扩展示例
/*
if($mod=='cargo'){
    #fldView::relat("relpb,fm[catid],fm[brand]","fm[xinghao],$mod,$did"); 
}
*/
