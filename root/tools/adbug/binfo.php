<?php 
require dirname(__FILE__).'/_config.php'; 

if($qstr=='logout'){
  $_SESSION[$sess_id] = '';
}elseif($qstr=='dologin'){ 
  $user = @$_POST['user'];
  $pass = @$_POST['pass'];
  if($user==$out_user && $pass==$out_pass){
    $_SESSION[$sess_id] = 'pstools';
  }
}

function userInfo($type='local'){ 
  $a = array('HTTP_USER_AGENT','HTTP_X_REAL_FORWARDED_FOR','HTTP_X_FORWARDED_FOR','HTTP_CLIENT_IP','REMOTE_ADDR','HTTP_VIA'); 
  $s = ''; 
  foreach($a as $k) if(isset($_SERVER[$k])) $s .= "<li><i>$k:</i>{$_SERVER[$k]}</li>\n";
  $na = explode('.',$_SERVER['REMOTE_ADDR']);
  $b = array(
    'sinaapp.com'=>'http://int.dpool.sina.com.cn/iplookup/iplookup.php?ip=',
    'pconline.com'=>'http://www.ip138.com/ips138.asp',
    '1616.net'=>'http://chaxun.1616.net/ip.htm',
  );
  $s .= "<li><i>My Out IP:</i>";
  foreach($b as $k=>$v){ $s .= "<a href='$v' target='_blank'>@$k</a>\n"; }
  $s .= "</li>\n";
  return $s;
} 

function pageFrame($key,$ext=''){
  $s = '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>{text}</body></html>';
  return str_replace('{text}',"[$key] Support(".lang('tools.adcfg_yes').")! $ext ",$s);
}

if($qstr=='iframe') die(pageFrame('Iframe'));
if($qstr=='frame') die(pageFrame('Frameset'));

if(strstr($qstr,'phpinfo')){ 
  $no = str_replace('phpinfo','',$qstr);
  $no = empty($no) ? '1' : max(1,intval($no));
  phpinfo($no);
  die();
}
if($qstr=='fset'){
  echo <<<HTML
<!DOCTYPE html><html><head>
<meta charset="utf-8">
<title>frameset</title>
<frameset rows="36,*" cols="*" border="false" id="frames">
  <frame name="top" src="?frame">
  <frame name="main" src="?frame">
</frameset>
<noframes>
<body>
<p><?php lang('tools.binf_lowbrowser',0);?></p>
</body>
</noframes> 
HTML;
  die();
}
?>
<!DOCTYPE html><html><head>
<?php glbHtml::page('init',1); ?>
<title><?php lang('tools.binf_userenv',0);?>-(COOKIE,SERVER,phpinfo(4,8),Login,Logout)</title>
<link href="<?php echo PATH_SKIN; ?>/_pub/a_jscss/cinfo.css?v1" type='text/css' rel='stylesheet'/>
<script src="<?php echo PATH_SKIN; ?>/_pub/jslib/jsbase.js?v1"></script>

<style type="text/css">
.notice { font-size:small; color:#F00; background:#FF9; padding:5px; }
td.tip { font-size:small; color:#F00; background:#FF9; }
input.r { width:96%; }
/*binfo*/
section { margin: 5px; }
.css3 { font-size: larger; color: #009; padding:5px; border:5px solid #CCC; 
  text-shadow: 0px -3px 0px #FFF, 0px 2px 3px #333; border-radius: 32px; }
li { border-bottom: 1px solid #CCC; padding: 5px; margin: 1px 0px; }
li i { width: 150px; font-style: normal; display: inline-block; overflow: hidden; padding: 0px 3px; margin: 0px; }
i.w1 { width: 240px; }
i.w2 { width: 80px; }
p.test { font-weight: normal; border: 0px; text-align: left; background: #FFF; padding: 5px; }
b, strong, .b { font-weight: bolder; }
@media only screen and (max-width:767px) {
    .login span { display:block; padding:5px; }
}
</style>

</head>
<body class="divOuter">

<?php basLang::shead(lang('tools.adcfg_binfo')); ?>

  <div class="pa5"></div>
  <table width="100%" border="1" class="tblist">
    <?php tadbugNave(); ?>
    <tr class="tc">
      <td colspan="4">
      <a href="?" target="_self">[Home]</a> | <a href="?testError" target="_self">Error</a> | 
      <a href="?cookie" target="_self">COOKIE</a> | <a href="?server" target="_self">SERVER</a> | 
      <a  href='?phpinfo1' target='_blank'>phpinfo</a>(<a href='?phpinfo4' target='_blank'>4</a>,<a href='?phpinfo8' target='_blank'>8</a>) | 
      <a href="?login" target="_self">Login</a> | <a href="?logout" target="_self">Logout</a></td>
    </tr>
  </table>

<?php
if($qstr=='testError'){
  echo "<div class='ma10'>".lang('tools.binf_showerror')."<br>\n 
        --- ".lang('tools.binf_sererror')."<br>\n
        --- ".lang('tools.binf_sadmerror')."\n";
  $er1 = 234/0;
  $er2 = $er3;
  die('<p>-End-</p></div>');
}
if($qstr=='server'){
  echo '<div>';
  foreach($_SERVER as $k=>$v){ 
    echo "<li>$k: $v</li>\n";
  }
  echo '</div>';
}
if($qstr=='cookie'){
  echo '<div>';
  $a = explode('; ',$_SERVER['HTTP_COOKIE']);
  foreach($a as $k=>$v){ 
    echo "<li>$k: $v</li>\n";
  }
  echo '</div>';
}
?>
<?php if(in_array($qstr,array('login','logout','dologin'))){ ?>

  <?php if(@!strstr($_SESSION[$sess_id],'pstools')){ ?>
  <p class="title">Login ……</p>
  <form action="?dologin" method="post" target="_self">
    <ul class="tc pa10 login">
      <span>User <input type="text" name="user" id="user" value=""></span>
      <span>Pass <input type="password" name="pass" id="pass" value=""></span>
      <span><input type="submit" name="act" id="act" value="Login"></span>
    </ul>
  </form>
  <?php }else{ ?>
  <p class="tc pa10">Login OK!</p>
  <?php } ?>
  <p class="notice"><?php lang('tools.binf_usenotes',0);?></p>

<?php } ?>
<?php if($qstr=='binfo'){ ?>

  <p class="title">(PHP)User Info # <a href="#" onClick="show('plugs')">(js)Plugs</a></p>
  <ul>
    <?php echo userInfo(); ?> <?php echo '<li><i>Server Time:</i>'.date('r').'</li>'; ?>
    <li><i>(js)USER_AGENT:</i><span id="jsua">JS NOT Support(<?php lang('tools.binf_nosupport',0);?>JavaScript)!</span></li>
    <li id="jsnow"></li>
    <li id="jscook"></li>
    <li id="jsplat"></li>
  </ul>
  <p class="test" id="plugs" style="display:none"></p>

  <p class="title">Iframe/Frameset (<?php lang('tools.binf_frames',0);?>) &gt;&gt; <a href='?fset' target='_blank'>Open</a></p>
  <iframe src="?fset" height="60" style="width:48%;"></iframe>
  <iframe src="?iframe" height="60" style="width:48%;"></iframe>

  <p title="svg,canvas,audio,localStorage,contenteditable">Html5 (<?php lang('tools.binf_html5base',0);?>)</p>
  <span id="lsSection"> &nbsp; [localStorage]<?php lang('tools.binf_nosuplocal',0);?></span>
  <section style="width:120px; float:right;"> <svg height="100">
    <polygon points="50,10 20,90 100,30 10,30 80,90"
  style="fill:rgb(120,120,120);stroke:rgb(60,60,60);stroke-width:2;fill-rule:evenodd;" />
    </svg> </section>
  <section contenteditable="true" title="[contenteditable]"><?php lang('tools.binf_editp',0);?></section>
  <canvas id="h5Canvas" width="180" height="24"><?php lang('tools.binf_nosupport',0);?> HTML5[canvas]。</canvas>
  <section> Datalist:
    <input type="url" list="url_list" name="link" />
    <datalist id="url_list">
      <option label="W3School" value="http://www.W3School.com.cn" />
      <option label="Google" value="http://www.google.com" />
      <option label="Microsoft" value="http://www.microsoft.com" />
    </datalist>
    <br>
    Placeholder:
    <input type="text" id="fmdata[keywords]" name="fmdata[keywords]" placeholder="placeholder"  value="" />
  </section>
  <section style="clear:left">
    <ruby>中文 - 漢字 - 拼音
      <rt>zhongwen - ㄏㄢˋ - pinyin</rt>
    </ruby>
  </section>
  <details>
    <summary>summary & details</summary><?php lang('tools.binf_details',0);?></details>

  <p class="title">CSS3 (<?php lang('tools.binf_css3r',0);?>)</p>
  <section class='css3'> 看到[圆角/阴影]效果了吗？没有看到，表示不支持CSS3。</section>
  <section></section>

  <p class="title">字体样式:span:bold # <a href="#" onClick="show('fonts')"><?php lang('tools.binf_detail',0);?></a></p>
  <p class="test" id="fonts" style="display:none"> 字体样式：默认-Default:<br>
    <strong>字体样式：Tag-strong；</strong><br>
    <b>字体样式：Tag-b；</b><br>
    <span style="font-weight:bold">字体样式：span:bold:</span><br>
    <span style="font-weight:bolder">字体样式：span:bolder:</span><br>
    <span style="font-weight:100">字体样式：span:100:</span><br>
    <span style="font-weight:300">字体样式：span:300:</span><br>
    <span style="font-weight:500">字体样式：span:500:</span><br>
    <span style="font-weight:700">字体样式：span:700:</span><br>
    <span style="font-weight:900">字体样式：span:900:</span></p>

<script>
function plugs(){ //获取插件所有的名称
   var info = "";
   var plugins = navigator.plugins;
   if (plugins.length>0) {  
     for (i=0; i < navigator.plugins.length; i++ ) {  
     info += ''+(i+1)+': '+navigator.plugins[i].name+";<br>";
   } } 
   jsElm.jeID('plugs').innerHTML = info;
}
function show(id){
  var d = jsElm.jeID(id);
  var v = d.style.display;
  d.style.display = v=='none' ? '' : 'none';
}
jsElm.jeID('jsua').innerHTML = navigator.userAgent;
jsElm.jeID('jsnow').innerHTML = '<i>Client Time(js):</i>'+new Date().toLocaleString();
jsElm.jeID('jscook').innerHTML = '<i class="w1">navigator.cookieEnabled:</i>'+navigator.cookieEnabled;
jsElm.jeID('jsplat').innerHTML = '<i class="w1">navigator.platform:</i>'+navigator.platform;
var lsSupport = (('localStorage' in window) && (window['localStorage'] !== null));
if(lsSupport) jsElm.jeID('lsSection').innerHTML = '&nbsp;<?php lang('tools.binf_suplocal',0);?>[localStorage]。';
var h5cnv = jsElm.jeID("h5Canvas");
var h5cxt = h5cnv.getContext("2d");
h5cxt.moveTo(10,1);
h5cxt.lineTo(70,20);
h5cxt.lineTo(10,20);
h5cxt.stroke();
plugs();
</script>
<?php } ?>

</body>
</html>
