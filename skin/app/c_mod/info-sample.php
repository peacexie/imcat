<?php 
glbHtml::page('Demo-Code/Sample',1);
glbHtml::imsub('imjq');
$fcall = file_get_contents(__FILE__);
$fcjs = basElm::getVal($fcall,'script');
$sign = safComm::signApi('init').'&debug=1';
include(vopTpls::pinc("c_mod/info-css",'.htm'));
?>
</head><body>

<h3>Demo-Code :: Demo-Effect</h3>

<div class="help_cont">
  <h4>Demo / Effect</h4>
  <ul id='demo_list'>
    <li>list</li>
  </ul>
  <h4>Code (js)</h4>
  <pre><?php echo basStr::filForm($fcjs); ?></pre>
  <ul>
  <?php
  echo "<p><b>Demo-Data-List</b></p>\n"; 
  $arr = array('mod=info&act=sys','mod=demo','mod=news&stype=nsystem','mod=demo&sfkw=are&sfop=lb');
  foreach ($arr as $val) {
      echo "<li><a href='?$val&$sign".(strpos($val,'&')?'':'&debug=0')."'>?$val</a></li>\n";
  }
  echo "<p><b>Demo-Data-Detail</b></p>\n"; 
  foreach (array('news','demo') as $mod) {
      $ofst = mt_rand(1,9);
      $list = $db->table("docs_$mod")->where('`show`=1')->limit("$ofst,2")->select();
      foreach($list as $key => $row) {
          echo "<li><a href='?mod=$mod&id={$row['did']}&$sign'>{$row['title']}</a></li>\n";
      }
  }
  echo "<p><b>Demo-Error</b></p>\n"; 
  $arr = array('mod=demo','mod=nomodel','mod=indoc','mod=info&act=noact','mod=demo&id=noexistid');
  foreach ($arr as $val) {
      echo "<li><a href='?$val".(($val=='mod=demo')?'':"&$sign")."'>?$val</a></li>\n";
  }
  ?>
  </ul>
</div>

<script>
$(function(){
    var sign = '<?php echo safComm::signApi('init'); ?>'; // &stype=nsystem
    var rurl = "<?php echo surl('app:0','',1); ?>?mod=news&psize=8&retype=jsonp&"+sign;
    $.get(
        rurl, {_test1: 'tester'}, 
        function (data) { 
            var html = ''; 
            for(var i in data){ 
                var url = '?mod=news&id='+data[i].did+'&'+sign;
                html += '<li><a href="'+url+'">'+data[i].title+'</a></li>';  
            }
            $('#demo_list').html(html);
        }, 'jsonp'
    );
}); 
</script>

</body></html>
