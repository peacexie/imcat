<?php (!defined('RUN_INIT')) && die('No Init');?>

<?php switch($part){ case '_test1_': ?>

En: Less use of the large section of the text:<?php echo $uarr['hname'] ?>

<?php break;case 'fldedit_note': 

$note  = "Fmt1:val=title,each item a line;\n";
$note .= "Fmt2:ModId(Catalog/Types);\n";
$note .= "Fmt3:pid:\"cnhn\",w:640;\n";
$note .= "Fmt4:bext_paras.logmode_cn, get cfgs from bext_paras;\n";
$note .= "[options array]config as below:\n";
$note .= "*. [Select][Checkbox][Radio]Fmt1/2/3:\n";
$note .= "*. [WinPick][WinPick-Multi]Fmt3:";
$reinc[$part] = $note;

break;case 'userm_empw': 

$data = "Hi,{$uarr['uname']}! <br><br>\n\n";
$data .= "Welcome use get-password by Email!<br>\n"; //  {$uarr['sys_name']}
$data .= "Please click(or copy) the url, and view the page:<br>\n";
$data .= "{$uarr['url']}<br>\n";
$data .= "Get passowrd by these tips.<br>\n<br>\n";
$data .= "{$uarr['sys_name']} ".date('Y-m-d H:i:s')."<br>\n";
$reinc[$part] = $note;

break;case 'plus_upbat': ?>
    
Notice: <br>
***1. This idea from <a href="http://www.babytree.com/">Babytree</a>, 
      First is asp version, then the two php versions; <br>
***2. Please set the categories, and then browse pictures; use below (+n) button to increase image project; 
      <br>it can set 96 pictures once a time; <br>
***3. This program is a value-added process, free of free; please do not demand it's function; 
      <br>if you can not meet your needs, please add the information in the ordinary; <br>
***4. Recommended put the file in same folder, use the title of the picture as the file name;
      <br>Can not use spaces, such as quotation marks, and other special characters in the file name;
      <br>The picture name can be used in Chinese, the directory do NOT use chinese.

<?php break;case 'plus_fview': ?>   

Notice: <br>
1. BIG files please upload by FTP, Can view [Admin-HELP] or [<a href="#readme.txt" target="_blank">File/Directory Structure</a>]; <br>
2. Use [View &gt;&gt; Copy-Link] get the url; <br>
3. [Temp] The new upload files, are put here, they will be moved to the relevant folders after save; <br>
4. [Now] Show this item while EDIT infos, these files are belong the info; <br>
5. [Upload] Batch/Single upload file(s); <br>
6. [Insert] will insert: iframe, map, swf, audio, video;

<?php break;case 'wex_user': ?>  

<tr><th>Nick</th><th>OpenId</th><th>Group-ID</th><th>City</th><th>Pic</th><th>Sex</th><th>Add-Time</th><th>Send-Message</th></tr>

<?php break;case '--end--': ?>  

-end-

<?php } ?>