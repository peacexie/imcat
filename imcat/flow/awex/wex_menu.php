<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
require __DIR__.'/_wex_cfgs.php';

//$types = array('test'=>'测试号','chking'=>'未认证','dingyue'=>'订阅号','fuwu'=>'服务号');
$tabid = 'wex_menu'; //$weapp
$mucfg = wysMenu::getMenuData($weapp); 
$cmop = basLang::ucfg('cfgbase.wx_mop');

if($view=='list'){ 
    
    $flgmusave = req('musave');
    $flgcreate = req('create');
    $flggetmnu = req('getmnu');
    $flgdelete = req('delete');

    if(!empty($flgmusave)){
        $whr = "`appid`='$weapp'"; //array('appid'=>$wecfg['appid']);
        foreach($fm as $k=>$v){
            if(empty($v['name'])){
                $db->table($tabid)->where("$whr AND `key`='$k'")->delete();
            }if(isset($mucfg[$k])){ 
                $db->table($tabid)->data(array('name'=>$v['name'],'val'=>$v['val']))->where("$whr AND `key`='$k'")->update();  
            }elseif(!empty($v['name'])){
                $v['kid'] = basKeyid::kidTemp(4).$k;
                $v['key'] = $k;
                $v['appid'] = $wecfg['appid'];
                $db->table($tabid)->data(basReq::in($v))->insert();
            }
        }
        $msg = "$cmop[save] : ".lang('flow.dops_ok');
        $mucfg = wysMenu::getMenuData($weapp); 
    }elseif(!empty($flgcreate)){
        $weixin = new wysMenu($wecfg); 
        $data = $weixin->create($mucfg); 
        $msg = $data['errcode'] ? lang('awex.fail')."<br>([$data[errcode]]$data[errmsg])" : lang('awex.success');
        die("<p class='tc'>$cmop[create] : $msg<br>".lang('awex.close')."<p>");
    }elseif(!empty($flggetmnu)){
        $weixin = new wysMenu($wecfg); 
        $data = $weixin->get(); 
        $menu = '';
        if(empty($data['errcode'])){
            foreach($data as $k=>$v){
                $title = "[$k]".$v['name']; //.''.$v['type'];
                $tiele = empty($v['val']) ? "<b>### $title</b>" : "$title (".$v['type'].")<br>".$v['val']."";
                $menu .= "\n $tiele";
            }
        }
        $msg = empty($data['errcode']) ? lang('awex.success')."<pre>$menu</pre>" : lang('awex.fail')."<br>([$data[errcode]]$data[errmsg])<br>";
        die("<p class='tc'>$cmop[get] : $msg ".lang('awex.close')."<p>");
    }elseif(!empty($flgdelete)){
        $weixin = new wysMenu($wecfg); 
        $data = $weixin->del(); 
        $msg = $data['errcode'] ? lang('awex.fail')."<br>([$data[errcode]]$data[errmsg])" : lang('awex.success');
        die("<p class='tc'>$cmop[del] : $msg<br>".lang('awex.close')."<p>");
    }
    
    eimp('/~base/cssjs/weixin.js?v=1');
    $umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
    glbHtml::tab_bar(lang('awex.pids')."[$wekid] : ".lang('awex.mcfg')." $umsg",$_cbase['run']['sobarnav'],40,'tl');
    
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>No.</th><th>".lang('awex.title')."</th><th>Key/Url</th>"; 
    echo "<th>".lang('awex.op')."</tr>\n";

    for($i=1;$i<=3;$i++){ for($j=0;$j<=5;$j++){
        $itemstr = ''; $mlen = 7; $imuid = "$i{$j}"; ///*[　][＋][－][｜][├][└]  */
        if($j==0 && $i<=2){
            $icon = "＋&nbsp;";
            $mlen = 4;
        }elseif($j==0 && $i==3){
            $icon = "＋&nbsp;";
            $mlen = 4;
        }elseif($i<=2){
            $icon = "｜ &nbsp; ";
            $icon .= "├-&nbsp;";
        }else{ //$i==3
            $icon = "　 &nbsp; ";
            $icon .= "├-&nbsp;";
        }
        $name = empty($mucfg[$imuid]['name']) ? '' : $mucfg[$imuid]['name'];
        $val = empty($mucfg[$imuid]['val']) ? '' : $mucfg[$imuid]['val'];
        $itemstr .= "<tr>";
        $itemstr .= "<td class='tc'>$imuid</td>\n";
        $itemstr .= "<td class='tl'>$icon<input name='fm[$imuid][name]' id='fm[$imuid][name]' value='$name' size='25' maxlength='".($j==0 ? 4 : 7)."' type='text'></td>\n";
        $itemstr .= "<td class='tl'><input name='fm[$imuid][val]' id='fm[$imuid][val]' value='$val' maxlength='240' type='text' class='w320'></td>";
        $itemstr .= "<td class='tc'><a id='cupick_$imuid' href='javascript:;' onClick=\"wxMenuClear($imuid)\">&lt;&lt;".lang('awex.clear')."</a></td>\n"; 
        $itemstr .= "</tr>";
        echo $itemstr;
    } }
    echo "
        <tr>
        <td>&nbsp;</td>
        <td colspan='2' class='tc' nowrap>
        <input name='musave' class='btn' type='submit' value='$cmop[save]' />
        &nbsp;
        <input name='create' class='btn' type='button' value='$cmop[create]' onclick=\"winOpen('$aurl[1]&create=1','$cmop[create]',360,240);\" />
        &nbsp;
        <input name='getmnu' class='btn' type='button' value='$cmop[get]' onclick=\"winOpen('$aurl[1]&getmnu=1','$cmop[get]',480,360);\" />
        &nbsp;
        <input name='delete' class='btn' type='button' value='$cmop[del]' onclick=\"winOpen('$aurl[1]&delete=1','$cmop[del]',360,240);\" />
        </td>
        <td>&nbsp;</td>
        </tr>";

    glbHtml::fmt_end(array("mod|$mod"));
        
}elseif($view=='form'){
    
}
?>
