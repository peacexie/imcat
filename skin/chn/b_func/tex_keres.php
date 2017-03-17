<?php
/*
单个模板扩展函数
*/ 
class tex_keres{ //extends tex_base
    
    #protected $prop1 = array();
    
    static function media_show($uvdo){ 
      if(empty($uvdo)) return;
      $cfg = comFiles::getTIcon($uvdo);
      $type = $cfg['type']=='audio' ? 'audio' : ($cfg['type']=='flash' ? 'swf' : 'ckvdo');
      if($type=='audio'){
          $w = 640; $h = 60;
      }else{
          $w = 640; $h = 480; 
      }
      $cstr = "{media:[type=$type][val=$uvdo][w=$w][h=$h][ext=true]/media}"; 
      $vdo = vopMedia::repShow($cstr);
      if($vdo) echo "<div class='keres-vdo'>$vdo</div>";
      return '';
    }
    
    static function down_show($ufile){
      if(empty($ufile)) return;
      $ticon = comFiles::getTIcon($ufile);
      $type = $ticon['type'];
      $icon = PATH_STATIC."/icons/file18/{$ticon['icon']}.gif";
      $icon = "<img src='$icon' width='18' height='18' border='0' align='absmiddle'>";
      $ufpath = comStore::revSaveDir($ufile);
      $ufdir = comStore::revSaveDir($ufile,'dir'); 
      $ufsize = filesize($ufdir);
      $ufsize = $ufsize ? basStr::showNumber($ufsize,'Byte') : '';
      $vpath = strstr($ufile,'root}') ? $ufile : substr($ufile,-56,56);
      $link = "<a href='$ufpath'>$icon [$type] $ufile</a> $ufsize ";
      if($link) echo "<div class='keres-down'>[附件下载] $link</div>";
      return '';
    }

}
