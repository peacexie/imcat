<?php 
require(dirname(__FILE__).'/_config.php');  

glbHtml::page("Run Plan - (sys_name)",1);
echo "<meta http-equiv='refresh' content='14400' />\n";
glbHtml::page('imin');
echo basJscss::imp("/plus/ajax/comjs.php?act=autoJQ");
echo basJscss::imp("/tools/exdiy/style.css");
echo basJscss::imp("/tools/exdiy/rplan.js");
glbHtml::page('body');

$ocfgs = glbConfig::read('outdb','ex');
$safix = $_cbase['safe']['safix'];
$sapp = $ocfgs['sign']['sapp'];
$skey = $ocfgs['sign']['skey'];

$act = basReq::val('act','');
include(vopShow::inc('/tools/exdiy/rplan.htm',DIR_ROOT));
glbHtml::page('end');
?>

<script>
// >請勿關閉! 定時XX 偵側程序

<?php echo "var urlp = '{$safix}[sapp]=$sapp&{$safix}[skey]=$skey';\n"; ?>
var pLists = [
    // configs
    /*
    new Array("clear_acts", "00:20"),
    new Array("clear_logs", "00:40"),
    new Array("clear_wex",  "01:00"),
    */
    <?php if($act=='utest') echo exvCron::plistTest(); ?>
]; 
<?php if($act!='alist') echo "setTimeout('timeSec()',1000);"; ?>

</script>

