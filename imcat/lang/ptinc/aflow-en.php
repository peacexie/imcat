<?php (!defined('RUN_INIT')) && die('No Init');?>

<?php switch($part){ case '_test1_': ?> 

En-Notice<?php echo $uarr['hname'] ?>
Multi language implementation, using this type of file to store multiple languages;
Without the use of multi language, these codes can be written directly into the script.

<?php break;case 'uc_indoc_list': ?> 

<th>Sel.</th><th>Title</th><th>Catalog</th><th>Show</th>
<th>Add-Time</th><th>Add-User</th><th>Edit-Time</th>
<th>Edit</th></tr>

<?php break;case 'docs_list': ?>

<th>Sel.</th><th>Title</th><th>Catalog</th><th>Show</th>
<th>Add-Time</th><th>Add-User</th><th>Edit-Time</th>
<th>Edit</th></tr>

<?php break;case 'advs_list': ?> 

<th>Sel.</th><th>Title</th><th>Catalog</th><th>Show</th>
<th>Url</th><th><?php echo ($uarr==4 ? 'uName' : 'Click'); ?></th>
<th>Add</th><th>Edit</th></tr>

<?php break;case 'adpush_list': ?> 

<th>Sel.</th><th>aid</th><th>Push Title</th><th>Catalog</th><th>Show</th>
<th>Max</th><th>Add</th><th>Edit</th><th>Push</th><th>Page</th><th>cfg/data</th></tr>

<?php break;case 'coms_list': ?> 

<th>Sel.</th><th>Title</th><th>Show</th><th>Nick</th>
<th>Add</th><th>Add-IP</th><th>Edit</th></tr>

<?php break;case 'notea_list': ?> 

<th>Sel.</th><th>Title</th><th>Type</th><th>Show</th><th>Nick</th>
<th>Add</th><th>IP</th><th>Edit</th></tr>

<?php break;case 'users_list': ?> 

<th>Sel.</th><th>UserID</th><th>Grade</th><th>Name</th>
<th>Show</th><th>Tel</th><th>E-Mail</th><th>imNo</th>
<th>Reg</th><th>Reg-IP</th>
<th>Edit</th></tr>

<?php break;case 'cargo_list': ?> 

<th>Sel.</th><th>Title</th><th>Catalog</th><th>Show</th>
<th>Add-Time</th><th>Edit-Time</th>
<th>Edit</th><th>Copy</th></tr>

<?php break;case 'demo_list': ?> 

<th>Sel.</th><th>Title/[Rem.][From][Wander][Work][Field]</th><th>Catalog</th><th>Show</th>
<th>Add-Time/uName</th><th>Edit-Time/IP</th><th>Tel/End-Time</th>
<th>Edit</th></tr>

<?php break;case 'indoc_list': ?> 

<th>Sel.</th><th>Title</th><th>Catalog</th><th>Dept.</th><th>Hot</th><th>Show</th>
<th>Add-Time</th><th>Add-User</th><th>Edit-Time</th>
<th>Edit</th></tr>

<?php break;case 'inread_list': ?> 

<th>Sel.</th><th>Doc.Title</th><th>Name</th><th>uName</th><th>ReadCnt</th>
<th>First-Read</th><th>First-IP</th>
<th>Last-Read</th><th>Last-IP</th>
<th>Edit</th></tr>

<?php break;case 'inrem_list': ?> 

<th>Sel.</th><th>Doc.Title</th><th>Rem.Title</th><th>uName</th>
<th>First-Read</th><th>First-IP</th>
<th>Edit</th></tr>

<?php break;case 'qarep_list': ?> 

<th>Sel.</th><th>QA-Title</th><th>Rep.Title</th><th>uName</th>
<th>Nick</th><th>imNo</th>
<th>Add</th><th>IP</th>
<th>Edit</th></tr>

<?php break;case 'qatag_list': ?> 

<th>Sel.</th><th>Tag</th><th>Hot</th>
<th>Add</th><th>IP</th>
<th>Upd</th><th>IP</th>
<th>Edit</th></tr>

<?php break;case 'cocar': ?> 

<th>Sel.</th><th>CargoID</th><th>OrderID</th>
<th>Count</th><th>uPrice</th>
<th>UserName</th>
<th>Add-Time</th><th>Edit</th></tr>

<?php break;case 'corder': ?> 

<th>Sel.</th><th>OrderNo</th><th>State</th><th>Fee-All</th><th>Count</th><th>Fee-Total</th>
<th>TraceNo</th>
<th>UserName</th><th>Tel</th>
<th>Add</th><th>Edit</th></tr>

<?php } ?>