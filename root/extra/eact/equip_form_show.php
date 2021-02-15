<?php
namespace imcat;
(!defined('RUN_DOPA')) && die('No DopA'); 
 
if(!empty($did)){
    $fmo = $db->table($dop->tbid)->where("did='$did'")->find();
    $fme = $dop->tbext ? $db->table($dop->tbext)->where("did='$did'")->find() : [];
    $fme && $fmo = $fmo + $fme;
    $isadd = 0;
    $apos = basElm::text2arr($fmo['npos']);
}else{
    $fmo = array();
    $isadd = 1;
    $apos = ['P001'=>''];
} 

//dump($apos);
$itpl = "<div id='ps_i[x]'>[x]<input id='ps[x]' onblur='editPval()' name='ps[x]' type='text' value='(v)' class='txt' maxlength='48' style='width:360px'; /><br></div>";

$dop->fmo = $fmo;
glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
$dop->fmCatid();

    $modcfgs = glbConfig::read($mod); 
    $mfields = $modcfgs['f']; 
    #fldView::lists($mod,$fmo);
    $skip = ['ndb_xxx'];
    foreach($mfields as $k=>$v){
        if(in_array($k, $skip)){ continue; }
        $item = fldView::fitem($k, $v, $fmo);
        $item = fldView::fnext($mfields, $k, $fmo, $item, $skip);
        $iext = ''; 
        if($k=='npos'){
            $item = str_replace("style='", "style='display:none;", $item); 
            for($i=1;$i<=count($apos);$i++){
                $ik = 'P'.substr("00$i",-3); $ival = isset($apos[$ik]) ? $apos[$ik] : '';
                $iext .= str_replace(["[x]","(v)"], [$ik,$ival], "$itpl\n"); 
            }
        }
        glbHtml::fmae_row($v['title'], $iext.$item);
    }

$dop->fmProp();
glbHtml::fmae_send('bsend',lang('flow.dops_send'));
//echo "<div id='itpl'>$itpl</div>";

?>

<script>
var apos = <?=comParse::jsonEncode($apos);?>, 
    itpl = "<?=$itpl?>", max = 96;

function editPval(){
    var celm = $("input[id='fm[ncnt]']").val(),
        spos = '', cnt = parseInt(celm);
    for(var i=1;i<=cnt;i++){ // P003
        var ik = 'P'+('00'+i).substr(-3);
        spos += ik + '=' + $("#ps"+ik).val() + '\n';
    }
    $("textarea[id='fm[npos]']").val(spos);
}

function setNpos(elm){
    var scnt = $(elm).val();
    var r = /^[1-9][0-9]*$/gi;
    if(!r.test(scnt)){
        alert('请输入数字!');
        return;
    }
    var cnt = parseInt(scnt); //console.log(cnt);
    if(cnt>max){
        alert('数量太大!');
        $(elm).val(max);
        cnt = max;
    }
    for(var i=1;i<=max;i++){
        var ik = 'P'+('00'+i).substr(-3), idiv = $('#ps_i'+ik);
        if(i<=cnt){
            if($(idiv).length==0){ 
                var tpl = itpl.replace(/\[x\]/g,ik).replace('(v)','');
                $("textarea[id='fm[npos]']").before(tpl);
            }else{ 
                $('#ps_i'+ik).show();
            }
        }else{ 
            $('#ps_i'+ik).hide();
        }
    }
}
$(function(){ // blur,click
    $("input[id='fm[ncnt]']").blur(function(e){
        setNpos(this);
    });
});

</script>

<?php

glbHtml::fmt_end(array("mod|$mod","isadd|$isadd"));

/*
    $jscfgs = "var att_vals=".comParse::jsonEncode($att_vals).";\n";
    echo basJscss::jscode("{$jscfgs}");
*/
?>
