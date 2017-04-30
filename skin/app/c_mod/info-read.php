<?php 
glbHtml::page('AppServer说明');
glbHtml::imsub('imjq');
$text = comFiles::get(vopTpls::pinc("c_mod/info-read",'.txt')); 
$text = extMkdown::pdext($text);
$fcall = file_get_contents(__FILE__);
$fcjs = basElm::getVal($fcall,'script');
?>
<style type="text/css">
body{font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:14px;color:#333;background-color:#fff;}
#help_cont{ max-width:760px; line-height:150%; margin:10px auto 10px auto; }
h2{ text-align: center; }
#help_cont h4 { display:block; font-size:18px; color:#555; padding:10px 10px 0 10px; margin: 10px 10px 0 40px; border-top: 1px solid #CCC; }
h4:before { display: inline-block; content:"◎◎"; }
pre { background:#EEE; border:1px solid #CCC; padding:10px; margin:5px 10px 10px 40px; }
</style>
<base target="_blank" />
</head><body>

<div id="help_cont">
  <?php echo $text; ?>
  <h4>Demo / Effect</h4>
  <ul id='demo_list'>
    <li>list</li>
  </ul>
  <h4>Code (js)</h4>
  <pre><?php echo basStr::filForm($fcjs); ?></pre>
</div>

<script>
$(function(){
    var sign = '<?php echo safComm::signApi('init'); ?>'; // &stype=nsystem
    var rurl = '<?php echo surl('app:0','',1); ?>?mod=news&psize=8&retype=jsonp&'+sign;
    $.get(
        rurl, {_test1: 'tester'}, 
        function (data) { 
            var html = ''; 
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
