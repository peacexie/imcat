<!DOCTYPE html><html><head>
<meta charset="utf-8">
<title>AppServer说明</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
glbHtml::page('imp');
$text = comFiles::get(vopTpls::pinc("c_mod/info-read",'.txt')); 
$text = extMkdown::pdext($text);
$fcall = file_get_contents(__FILE__);
$fcjs = basElm::getVal($fcall,'script');
//print_r($fcjs);
?>
<style type="text/css">
  #help_cont{ max-width:760px; line-height:150%; margin:10px auto 10px auto; }
  h3{ text-align: center; }
  #help_cont h4 { display: block; padding: 10px 10px 1px 10px; margin: 10px 10px 1px 40px; border-top: 1px solid #CCC; }
  h4:before { display: inline-block; content:"◎◎"; color:#036; }
</style>
</head><body>

<div id="help_cont">
  <?php echo $text; ?>
  <h4>Demo / Effect</h4>
  <ul id='demo_list'>
    <li>list</li>
  </ul>
  <h4>Code (js)</h4>
  <ul id='code_area'>
    <li><pre><?php echo basStr::filForm($fcjs); ?></pre></li>
  </ul>
</div>

<script>
$(function(){
    var sign = '<?php echo safComm::signApi('init'); ?>'; // &stype=nsystem
    var rurl = '<?php echo surl('app:0','',1); ?>?mod=news&psize=8&retype=jsonp&'+sign;
    $.get(
        rurl, {_test1: 'tester'}, 
        function (data) { 
            var html = ''; //console.log(data); 
            for(var i in data){ 
                var url = '?mod=news&id='+data[i].did+'&'+sign;
                html += '<li><a href="'+url+'" target="_blank">'+data[i].title+'</a></li>';  
            }
            $('#demo_list').html(html);
        }, 'jsonp'
    );
}); 
</script>

</body></html>
