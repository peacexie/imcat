<?php if($part=='_test1_'){ ?>

En-Notice<?php echo $uarr['hname'] ?>
Multi language implementation, using this type of file to store multiple languages;
Without the use of multi language, these codes can be written directly into the script.

<?php }elseif($part=='uc_indoc_list'){ ?> 

<th>Sel.</th><th>Title</th><th>Catalog</th><th>Show</th>
<th>Add-Time</th><th>Add-User</th><th>Edit-Time</th>
<th>Edit</th></tr>

<?php }elseif($part=='docs_list'){ ?>

<th>Sel.</th><th>Title</th><th>Catalog</th><th>Show</th>
<th>Add-Time</th><th>Add-User</th><th>Edit-Time</th>
<th>Edit</th></tr>

<?php }elseif($part=='advs_list'){ ?> 

<th>Sel.</th><th>Title</th><th>Catalog</th><th>Show</th>
<th>Url</th><th><?php echo ($uarr==4 ? 'uName' : 'Click'); ?></th>
<th>Add</th><th>Edit</th></tr>

<?php }elseif($part=='coms_list'){ ?> 

<th>Sel.</th><th>Title</th><th>Show</th><th>uName</th>
<th>Tel</th><th>E-Mail</th><th>imNo</th>
<th>Add</th><th>Add-IP</th>
<th>Edit</th></tr>

<?php }elseif($part=='users_list'){ ?> 

<th>Sel.</th><th>UserID</th><th>Grade</th>
<th>Name</th><th>Tel</th><th>E-Mail</th><th>imNo</th>
<th>Reg</th><th>Reg-IP</th>
<th>Edit</th></tr>

<?php }elseif($part=='cargo_list'){ ?> 

<th>Sel.</th><th>Title</th><th>Catalog</th><th>Show</th>
<th>Add-Time</th><th>Edit-Time</th>
<th>Edit</th><th>Copy</th></tr>

<?php }elseif($part=='demo_list'){ ?> 

<th>Sel.</th><th>Title/[Rem.][From][Wander][Work][Field]</th><th>Catalog</th><th>Show</th>
<th>Add-Time/uName</th><th>Edit-Time/IP</th><th>Tel/End-Time</th>
<th>Edit</th></tr>

<?php }elseif($part=='indoc_list'){ ?> 

<th>Sel.</th><th>Title</th><th>Catalog</th><th>Dept.</th><th>Hot</th><th>Show</th>
<th>Add-Time</th><th>Add-User</th><th>Edit-Time</th>
<th>Edit</th></tr>

<?php }elseif($part=='inread_list'){ ?> 

<th>Sel.</th><th>Doc.Title</th><th>Name</th><th>uName</th><th>ReadCnt</th>
<th>First-Read</th><th>First-IP</th>
<th>Last-Read</th><th>Last-IP</th>
<th>Edit</th></tr>

<?php }elseif($part=='inrem_list'){ ?> 

<th>Sel.</th><th>Doc.Title</th><th>Rem.Title</th><th>uName</th>
<th>First-Read</th><th>First-IP</th>
<th>Edit</th></tr>

<?php }elseif($part=='qarep_list'){ ?> 

<th>Sel.</th><th>QA-Title</th><th>Rep.Title</th><th>uName</th>
<th>Nick</th><th>imNo</th>
<th>Add</th><th>IP</th>
<th>Edit</th></tr>

<?php }elseif($part=='qatag_list'){ ?> 

<th>Sel.</th><th>Tag</th><th>Hot</th>
<th>Add</th><th>IP</th>
<th>Upd</th><th>IP</th>
<th>Edit</th></tr>

<?php }elseif($part=='cocar'){ ?> 

<th>Sel.</th><th>CargoID</th><th>OrderID</th>
<th>Count</th><th>uPrice</th>
echo "<th>UserName</th>
<th>Add-Time</th><th>Edit</th></tr>

<?php }elseif($part=='corder'){ ?> 

<th>Sel.</th><th>OrderNo</th><th>State</th><th>Fee-All</th><th>Count</th><th>Fee-Total</th>
<th>TraceNo</th>
<th>UserName</th><th>Tel</th>
<th>Add</th><th>Edit</th></tr>

<?php } ?>