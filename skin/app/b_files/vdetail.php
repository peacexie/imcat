<?php

$pmod = $_groups[$this->mod]['pid'];
$vars = $dext = array();
$mfile = vopTpls::pinc("c_mod/vdetail-{$this->mod}"); 

if(file_exists($mfile)){ // 扩展
    include $mfile;
}elseif(in_array($pmod,array('docs','users','coms','advs','types'))){
    $tabid = glbDBExt::getTable($this->mod);
	$whrid = tex_func::detail_kid($pmod,$this->id);
    $vars = $db->table($tabid)->where($whrid)->find();
    if(empty($vars)){ 
        $vars = $this->error("Error-ID(vdetail):{$this->id}");
    }
    $detail = req('detail','');
    if($detail && in_array($pmod,array('docs'))){
        $tabid = glbDBExt::getTable($this->mod,1);
		$whrid = tex_func::detail_kid($pmod,$this->id,0);
        $dext = $db->table($tabid)->where($whrid)->find();
        $dext && $vars += $dext; 
    }
}else{
    $vars = $this->error("Error-Mod(vdetail):{$this->mod}");
}

// deal:vars
// fields,
