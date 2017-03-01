<!DOCTYPE html><html><head>
<meta charset="utf-8">
<title>Hi, AppAppServer!</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name='robots' content='noindex, nofollow'>
<style type="text/css">
  #ind_cont{ max-width:760px; line-height:150%; margin:10px auto 10px auto; }
  h3{ text-align: center; }
</style>
</head><body>

<h3>Hi, AppAppServer!</h3>

<div id="ind_cont">
<?php

$sign = safComm::signApi('init').'&debug=1';

if(!empty($this->hcfgs['c']['debug'])){

    echo "<p><b>Demo-Data-List</b></p>\n"; 
    $arr = array('mod=info&act=read','mod=info&act=sys','mod=demo','mod=news&stype=nsystem','mod=demo&sfkw=are&sfop=lb');
    foreach ($arr as $val) {
        echo "<li><a href='?$val&$sign".(strpos($val,'&')?'':'&debug=0')."'>?$val</a></li>\n";
    }

    echo "<p><b>Demo-Data-Detail</b></p>\n"; 
    foreach (array('news','demo') as $mod) {
        $ofst = mt_rand(1,9);
        $list = $db->table("docs_$mod")->where('`show`=1')->limit("$ofst,3")->select();
        foreach($list as $key => $row) {
            echo "<li><a href='?mod=$mod&id={$row['did']}&$sign'>{$row['title']}</a></li>\n";
        }
    }

    echo "<p><b>Demo-Error</b></p>\n"; 
    $arr = array('mod=demo','mod=nomodel','mod=indoc','mod=info&act=noact','mod=demo&id=noexistid');
    foreach ($arr as $val) {
        echo "<li><a href='?$val".(($val=='mod=demo')?'':"&$sign")."'>?$val</a></li>\n";
    }

}
?>
</div>

</body></html>
