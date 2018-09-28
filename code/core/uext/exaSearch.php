<?php
namespace imcat;

function fsget_dirs($path) { 
    $now = BASE.$path; $s = '';
    $handle = opendir($now); 
    while($file=readdir($handle)){
      if(is_dir("$now/$file")&&($file!="."&&$file!="..")){
        if(!strstr(SKIP,";$file;")) 
          $s .= "\n<a href='?path=$path/$file' class='item'>/$file</a>";
    } }
    closedir($handle);
    return $s;
}

function fsget_file($path,$deep=0) { 
    global $dir,$skip,$ex1,$ex2,$key,$act,$file_arr; $now = BASE."/$path"; 
    $skip = str_replace(',',';',$skip);
    $skip = SKIP.";$skip;";
    $skip = str_replace(';;',';',$skip);
    $handle = opendir($now); 
    while($file=readdir($handle)){
      //$file = strtolower($file);
      if(is_file("$now/$file")){
        $f1 = 0; $f2 = 0;
        for($k=0;$k<count($ex1);$k++){
          if(strstr($file,"$ex1[$k]")){ 
            $f1++; break;
        } }
        if(!$f1){
              continue;
        }
        if($ex2){
          for($k=0;$k<count($ex2);$k++){
            if(strstr($file,"$ex2[$k]")){ 
              $f2++; break;
        } } }
        if($f2){
              continue;
        }
        $size = filesize("$now/$file")/1024;
        if($size>FMAX){
              continue;
        }
        
        if($key){
          if($act=='File'){
            $data = $file; 
            $cset = 'x';
          }else{
            $data = file_get_contents("$now/$file"); // null,lower 
            $data = strtolower($data); 
            $cset = mb_detect_encoding($data,array('ASCII','GB2312','BIG5','GBK','UTF-8'));
            $cset = str_replace('BIG-5','BIG5',$cset);
            if(!$cset){
              if(strstr($data,'charset=gb2312')) $cset = 'gb2312';
              if(strstr($data,'charset=gbk')) $cset = 'gbk';
              if(strstr($data,'charset=big5')) $cset = 'big5';
              if(strstr($data,'charset=utf-8')) $cset = 'utf-8';
            }
            if(!$cset) $cset = CSET;
            //if(strstr($data,'charset=big5')&&$cset='CP936') $cset = 'big5';
            //$cset = ($cset)?'utf-8':'y'; //is_utf8($data);
          }
          if(strstr($data,$key)){ 
            $size = round($size*100)/100; //round(xx/1024*100)/100 .' KB';
            $time = date("Y-m-d H:i",filemtime("$now/$file"));
            $link = "<a href='$path/$file'>$file</a>";
            $file_arr[] = array('file'=>"$path/$file",'size'=>$size,'time'=>$time,'cset'=>$cset);
          }
        }else{
            //
        }
      }
      if(is_dir("$now/$file")&&($file!="."&&$file!="..")){
        if(!strstr($skip,";$file;")){ 
          if($deep==0){
            $f3 = 0; 
            for($k=0;$k<count($dir);$k++){
              if(strstr("$path/$file","$dir[$k]")){     
                $f3++; break;
              }
            }
            if($f3) fsget_file("$path/$file",$deep+1);  
          }else{
            fsget_file("$path/$file",$deep+1);  
          }
      } } 
    } 
    closedir($handle);
    //return $file_arr;
}

function fsdir_cbox($path) { //  checked="checked"
    global $redir,$dir;
    $now = BASE.$path; $s = '';
    $handle = opendir($now); 
    while($file=readdir($handle)){
      if(is_dir("$now/$file")&&($file!="."&&$file!="..")){
        if(!strstr(SKIP,";$file;")){
          if(!strstr($redir,"$file")) $ckstr = '';
          else $ckstr = 'checked="checked"';
          if(in_array($file,$dir)) $ckstr = 'checked="checked"';
          $s .= "<span class='item'><input type='checkbox' name='dir[]' id='dir[]' value='$file' $ckstr />$file </span>";
    } } } 
    closedir($handle);
    return $s;
}
function fsext_list($n) { 
    global $reex1,$reex2,$ex1,$ex2;
    if($n==1) $reext = $reex1;
    if($n==2) $reext = $reex2;
    $a = explode(';',FEXT); $s = '';
    for($i=0;$i<count($a);$i++){
        if(!strstr($reext,$a[$i])) $ckstr = '';
        else $ckstr = 'checked="checked"';
        if($n==1&&in_array(".$a[$i]",$ex1)) $ckstr = 'checked="checked"';
        if($n==2&&in_array(".$a[$i]",$ex2)) $ckstr = 'checked="checked"';
        $s .= "<span class='iext'><input type='checkbox' name='ex{$n}[]' id='ex{$n}[]' value='.$a[$i]' $ckstr />.$a[$i] </span>";
    }
    return $s;
}

