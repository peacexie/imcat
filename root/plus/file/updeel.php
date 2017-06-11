<?php
require '_config.php'; 
usrPerm::run('pextra','edtup'); //上传权限 

$recbk = req('recbk','ref'); 
$udata = req('udata',''); 
$uptype = req('uptype','');
$uparr = array(); 
if($udata && in_array($uptype,array('remote','base64'))){
    $uparr[] = $uptype=='remote' ? $udata : 'udata';
}elseif($_FILES){
    foreach($_FILES as $k=>$v){
        if($v['name'] && $v['type']){ 
            $uparr[] = $k;
        }
    } //array_keys($_FILES);
    $uptype = 'upload';
}else{
    die(lang('plus.fop_error'));    
}

if($uparr){ 
    
    $ucfg = usrPerm::pmUpload($user); 
    $config = array( //上传配置
        "maxSize" => $ucfg['upsize1'], //单位KB
        "allowFiles" => $ucfg['uptypes'], //array(".gif", ".png",'.jpg')
    );
    
    $nok = $smsg = ''; 
    $sum = count($uparr);
    foreach($uparr as $k){
    //生成上传实例对象并完成上传
        $up = new comUpload($k, $config, $uptype);
        $info = $up->getFileInfo(); 
        if($info['state']=='SUCCESS'){
            $nok++;
            $smsg .= "\\n{$info['original']} -=> ".basename($info['url'])."";
        }else{
            $smsg .= "\\n{$info['original']} -=> ".basename($info['state'])."";    
        }
    }

    if($recbk=='ref') {
        if($nok==0){
            $msg = lang('plus.fop_fupfail')."\\n$smsg";
        }else{
            $mok = $sum>1 ? ($mok==$sum ? lang('plus.fop_alln',$mok) : lang('plus.fop_alln',"$mok/$sum")) : ""; $mok = $mok ? "($mok)" : "";
            $msg = lang('plus.fop_fupok')."$mok \\n$smsg";    
        } 
        echo basJscss::Alert($msg,'Redir',basReq::getURep($_SERVER["HTTP_REFERER"],'dfile',''));
    }elseif($recbk=='pfield'){
        $cmd = $nok>0 ? "window.parent.jsElm.jeID('$fid').value='{$info['url']}';" : "alert('{$info['state']}');";
        echo basJscss::jscode("$cmd;parent.layer.close(parent.layer.getFrameIndex(window.name));"); 
    }elseif(substr($recbk,0,6)=='isbat_'){ 
        $id = substr($recbk,6);
        $cmd = $nok>0 ? " [OK!] ".lang('plus.fop_upok').":{$info['original']}" : " [Error] ".lang('plus.fop_upfail').": {$info['state']}";
        echo basJscss::jscode("window.parent.jsElm.jeID('bidiv_$id').innerHTML='[OK!] ".lang('plus.fop_upok').":{$info['original']}';"); 
    } else {
        echo json_encode($info);
    }
    
    exit(0);
}else{
    exit('Empty!');    
}

/** 得到上传文件所对应的各个参数,数组结构
[state] => SUCCESS
[url] => /08tools/yssina/1/dtmp/@udoc/4728452c2b935b3fdd531f90ad3c4c74/2015-1x-b9u53hm.jpg
[title] => 2015-1x-b9u53hm.jpg
[original] => gezi1-40x.jpg
[type] => .jpg
[size] => 1065
 */
