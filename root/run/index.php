<?php 
// 导入类库/导入配置
require('./_paths.php'); 
die();


// 获取ip,可在地址栏输入ip=xxxx用于调试
$userip = comSession::getUIP($nsp=''); //echo $ip;
if(!empty($_GET['ip'])) $userip = $_GET['ip']; //调试

// 获取ip对应地址
$ip = new extIPAddr();
$uaddr = $ip->addr($userip); //echo $uaddr;

// 查找需要跳转的url地址
$nurl = exvJump::getDir($uaddr);

// 调试: /index.php?ip=59.108.49.35&debug=1
if(!empty($_GET['debug'])){
    echo "userip = ($userip)<br>\n";
    echo "addr = ($uaddr)<br>\n";
    echo "dir_url = ($nurl)<br>\n";
// 获取分站数据：/index.php?sites=1
}elseif(!empty($_GET['sites'])){
    $data = exvJump::getSites();
    echo $data; //(自行根据需要修改代码...)
// 自动跳转
}else{
    header("Location:$nurl"); 
}

