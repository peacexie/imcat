<?php
require(dirname(__FILE__).'/_config.php'); 

// install;patch;updatedata;
define('SKIP',';.svn;_svn;.git;_git;'); 
define('SDIR',';@skipdir;');
define('BASE',DIR_PROJ);
// .log .js .asa .css .cs .config .inc .txt .as .asc .asr .vb .htaccess .htpasswd
define('FEXT','php;xml;txt;js;html;htm;css;tpl'); 
define('FMAX',1024); // 最大文件(KB)
define('CSET','utf-8'); // 默认编码(gb2312,gbk,big5,utf-8)

$file_arr = array();
include_once(DIR_CODE."/core/uext/exaSearch.php");

$path = isset($_REQUEST['path'])?$_REQUEST['path']:'';
$act = isset($_REQUEST['act'])?$_REQUEST['act']:'Form'; // Form,(Data,File),View,Light
$react = isset($_REQUEST['react'])?$_REQUEST['react']:'Data';

$dir = isset($_REQUEST['dir'])?$_REQUEST['dir']:''; if(!$dir) $dir = array('include'); 
$skip = isset($_REQUEST['skip'])?$_REQUEST['skip']:SDIR; 
$ex1 = isset($_REQUEST['ex1'])?$_REQUEST['ex1']:array('.php'); 
$ex2 = isset($_REQUEST['ex2'])?$_REQUEST['ex2']:array('.gif','.jpg','.jpeg','.png'); 
$key = isset($_REQUEST['key'])?$_REQUEST['key']:''; //echo $key;
$key = stripslashes($key); $keyBak = $key; $key = strtolower($key); 

$file = isset($_REQUEST['file'])?$_REQUEST['file']:'';
$cset = isset($_REQUEST['cset'])?$_REQUEST['cset']:'';
$cset2 = ($cset)?$cset:'utf-8';


?>
<!DOCTYPE html><html><head>
<meta charset="<?php echo $cset2; ?>">
<title>files-Search</title>
<style type="text/css">
body, td, th { font-size: 14px; line-height: 150%; }
.item, .iext { height: 12; float: left; overflow: hidden; border-bottom: 1px solid #CCC; white-space: nowrap; word-break: keep-all; padding: 1px; }
.item { width: 85px; margin: 0px 1px 0px 5px; }
.iext { width: 58px; margin: 0px 1px 0px 1px; }
.tab1 { width: 720px; margin: auto auto 12px auto; }
.subj { font-weight: bold; color: #333; background-color: #DDD; }
table.tab2 { background-color: #CCC; margin: 5px; }
table.tab2 td { background-color: #FFF; line-height: 120%; }
table.tab2 tr.subj td { background-color: #DDD; }
</style>
<script src='<?php echo PATH_ROOT; ?>/plus/ajax/comjs.php?_r=1456459912'></script>
<script src='<?php echo PATH_ROOT; ?>/skin/jslib/search.js?_r=1456459912'></script>
<style type="text/css">
.highlight { background: green; font-weight: bold; color: white; padding: 5px 2px; }
</style>
</head>
<body>
<?php
if(strstr('(View,Light)',$act)){ 
  // mb_convert_encoding ($fContents, $to, $from);
  header("content-Type: text/html; charset=$cset2;");
  //$key = str_replace('function ','',$key);
  echo "\n<b>File:$file</b> ( <span id='div_key_peace_xieys' style='color:#30F'></span> )<hr>"; 
  if($act=='Light'){
    highlight_file(BASE."/$file");
  }else{
	$data = file_get_contents(BASE."$file"); // null,lower
	echo "<pre>".htmlspecialchars($data,1)."\n</pre>\n"; 
  }
  $divkey = "document.getElementById('div_key_peace_xieys').innerHTML = 'keywords: $keyBak';";
  echo "<script type='text/javascript'>var n=0; schDone('$keyBak'); $divkey</script>";
  die("\n<body>\n<html>\n");
}
?>
<table border="1" class="tab1">
    <tr>
        <td width="18%" align="center"><strong><?php lang('tools.so_title',0); ?></strong></td>
        <td align="right"><a href="?">Refresh</a> &nbsp; </td>
    </tr>
</table>
<?php if($act=='Form'){ ?>
<table border="1" class="tab1">
    <form id="fm1" name="fm1" method="post" action="?">
        <tr>
            <td align="right">path.路径：<br />
                [<a href="<?php echo "?path=$path&key=$key"; ?>"><?php lang('tools.so_refresh',0); ?></a>]：</td>
            <td><table border="0" cellpadding="0" cellspacing="1">
                    <tr>
                        <td><a href='?dir=' class='item'>(Root)</a><?php echo fsget_dirs($path); ?></td>
                    </tr>
                    <tr>
                        <td><input name="path" type="text" id="path" value="<?php echo $path; ?>" size="56" /></td>
                    </tr>
                </table></td>
        </tr>

        <tr>
            <td align="right">case.方案：</td>
            <td><span class='item'>
                <input type="checkbox" onclick="schCase(this,'.php.class.tpl','.htm.html.txt.xml.css.js','code,core,adpt,flow','')" />
                <?php lang('tools.so_ptext',0); ?> </span> <span class='item'>
                <input type="checkbox" onclick="schCase(this,'.php.class.tpl','.htm.html.txt.xml.css.js','code,core','function ')" />
                <?php lang('tools.so_pfunc',0); ?> </span> <span class='item'>
                <input type="checkbox" onclick="schCase(this,'.php.class.tpl','.htm.html.txt.xml.css.js','cache,dynamic,pcache','$')" />
                <?php lang('tools.so_pvar',0); ?> </span> <span class='item'>
                <input type="checkbox" onclick="schCase(this,'.js.htm.html','.txt.xml.css','skin,jslib,a_jscss','')" />
                <?php lang('tools.so_js',0); ?> </span> <span class='item'>
                <input type="checkbox" onclick="schCase(this,'.htm.html.js.css.xml','.php','skin,jslib,a_jscss','')" />
                <?php lang('tools.so_html',0); ?> </span> <span class='item'>
                <input type="checkbox" onclick="schCase(this,'.php.tpl.js.css','.xml.txt.log','code,tpls,skin','class ')" />
                <?php lang('tools.so_tpl',0); ?> </span></td>
        </tr>
        <tr>
            <td align="right">dir.目录：</td>
            <td><?php echo fsdir_cbox($path); ?></td>
        </tr>
        <tr>
            <td align="right">skip.忽略： </td>
            <td><input name="skip" type="text" id="skip" value="<?php echo $skip; ?>" size="56" /></td>
        </tr>
        <tr>
            <td align="right">ext.后缀：</td>
            <td><?php echo fsext_list(1); ?></td>
        </tr>
        <tr>
            <td align="right">exu.排除：</td>
            <td><?php echo fsext_list(2); ?></td>
        </tr>
        <tr>
            <td align="right">key.关键字：</td>
            <td><input name="key" type="text" id="key" value="<?php echo $keyBak; ?>" size="56" /></td>
        </tr>

        <tr>
            <td width="18%" align="right"> Option.提交：</td>
            <td align="left"><select name="act" id="act">
                    <option value="Data">Data:内容</option>
                    <option value="File" <?php if($react=='File') echo ' selected="selected"'; ?>>File:文件</option>
                </select>
                <input type="submit" name="submit" id="submit" value="<?php lang('tools.so_send',0); ?>" />
                <?php lang('tools.so_tips',0); ?>
                <input type="hidden" name="dirs" id="dirs" value=";;" /></td>
        </tr>
    </form>
</table>
<script type="text/javascript"> 

</script>
<?php } ?>
<?php 
if(strstr('(Data,File)',$act)){ 
  $redir = ''; foreach($dir as $v) $redir .= ",$v";
  //if(count($ex1)==count(explode(';',FEXT))) $reex1 = '';
  //else { $reex1 = ''; foreach($ex1 as $v) $reex1 .= ",$v"; }
  $reex1 = ''; foreach($ex1 as $v) $reex1 .= ",$v";
  $reex2 = ''; foreach($ex2 as $v) $reex2 .= ",$v";
  $reskip = ($skip==SDIR)?'':$skip; 
?> 
<table border="1" class="tab1">
    <tr>
        <td><strong><?php lang('tools.so_res',0); ?></strong></td>
        <td colspan="2" align="center"><a href="?<?php echo "path=$path&skip=$reskip&key=$keyBak&react=$act&redir=$redir&reex1=$reex1&reex2=$reex2"; ?>"><?php lang('tools.so_back',0); ?></a></td>
    </tr>
    <tr>
        <td>Path-File</td>
        <td width="15%">Size(KB)</td>
        <td width="20%">Modify</td>
    </tr>
    <?php   
fsget_file($path);
for($i=0;$i<count($file_arr);$i++){
  $b = $file_arr[$i]; $f = $b['file']; $cset = $b['cset'];
  if(strlen($f)>50) $fname = substr($f,0,24).'(...)'.substr($f,strlen($f)-24);
  else $fname = $f;
?>
    <tr>
        <td><a href="?<?php echo "act=View&file=$f&key=$keyBak&cset=$cset"; ?>" target="_blank"><?php echo $fname; ?></a></td>
        <td><?php echo $b['size'] ?></td>
        <td nowrap="nowrap"><a href="?<?php echo "act=Light&file=$f&key=$keyBak&cset=$cset"; ?>" target="_blank"><?php echo $b['time'] ?></a></td>
    </tr>
    <?php
}
?>
</table>
<?php } ?>
</body>
</html>
