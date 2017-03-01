<?php
if($this->act=='sys'){
    foreach ($_cbase as $key => $value) {
        if(in_array($key,array('safe','tpl','debug','ucfg','weixin')))
            unset($_cbase[$key]);
    }
    $vars = $_cbase;
}elseif($this->act=='read'){
    $vout = 0; // 不用系统输出
    $fp = vopTpls::pinc("c_mod/info-read");
    if(file_exists($fp)){
        include($fp);
    }
}else{
    $this->vars = $this->error("Error-Act:{$this->act}");
    $this->view('~');
}
