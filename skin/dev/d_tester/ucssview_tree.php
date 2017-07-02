
<?php 
(!defined('RUN_INIT')) && die('No Init');
imp('/_pub/a_jscss/sordbar.js');
imp('/_pub/jslib/jspop.js'); 
?>
<!--//plus/ajax/comjs.php?act=jsTypes:cargo,brand,hinfo;jsRelat:relpb;jsFields:cargo&cmod=brand/--> 
<script src='<?php echo PATH_ROOT; ?>/plus/ajax/comjs.php?act=jsTypes:cargo,china,natcn;'></script>

<table border="1" align="center" cellpadding="5" cellspacing="5">
    <tr>
        <td colspan="2" align="center" valign="top"><a href="?">[Refresh]</a></td>
    </tr>
    <tr>
        <td width="240" rowspan="2" valign="top" class="layTree1" id="tree01">layTree1</td>
        <td height="80" valign="top">
<div id="typlays"></div>
<div id="typlay2"></div>
<div id="typlay3"></div>
        </td>
    </tr>
    <tr>
        <td width="240" valign="top"><div class="layTree7" id="tree02">layTree1</div></td>
    </tr>
</table>
<script>

var tpl1 = "<p class='tree_(level) p_(pid) k_(key) (css)'>(lay)<a href='?china=(key)'>[(letter)](title)</a></p>"; 
var tpl2 = "<p class='tree_(level) p_(pid) k_(key) (css)'>(lay)<a href='?cargo=(key)'>(title)</a></p>"; 
var trees1 = sotree_init(tpl1,'china',0); $('#tree01').html(trees1); // ,'tree01'
var trees2 = sotree_init(tpl2,'cargo',0); $('#tree02').html(trees2); 

sotree_act('tree01','china',urlPara('china','c0755')); // zx,gd,c0769,tw,cmazu
sotree_open('tree02',urlPara('cargo','p2012'));  

mselInit('typlays','fmxid','china','c0769','省份,地区');
mselInit('typlay2','fmyid','china');
mselInit('typlay3','fmzid','natcn');

</script>

