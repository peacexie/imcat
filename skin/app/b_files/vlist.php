<?php

$pmod = $_groups[$this->mod]['pid'];
$mcfg = read($this->mod);
$mfile = vopTpls::pinc("c_mod/vlist-{$this->mod}"); 

$whrarr['show'] = tex_func::list_show($pmod,$mcfg);
$whrarr['stype'] = tex_func::list_stype($pmod,$mcfg);
$whrarr['pid'] = tex_func::list_pid($pmod,$mcfg);
$whrarr['sfkw'] = tex_func::list_sfkw($pmod,$mcfg);

// inc:_config/vc_mod.php
$cfile = vopTpls::pinc("_config/vc_{$this->mod}"); 
if(file_exists($cfile)){ // 扩展
    include($cfile);
    $ckey = "_vc_{$this->mod}"; $ckey = $$ckey;
    $ccfg = empty($ckey['list']) ? array() : $ckey['list'];
    tex_func::list_excfg($whrarr,$ccfg);
}
$whrstr = '';
foreach ($whrarr as $key => $value) {
    $whrstr .= $value;
}
$limit = tex_func::list_limit($pmod,$mcfg);

if(file_exists($mfile)){ // 扩展
    include($mfile);
}elseif(in_array($pmod,array('docs','users','coms'))){ 
    $tabid = glbDBExt::getTable($this->mod);
    $vars = $db->table($tabid)->where($whrstr)->limit($limit)->select();
}elseif($pmod=='advs'){
    // adblock:abfoot0,2cF0F
    $adfpos = req('adfpos','4','N');
    $adfcol = req('adfcol','F0F','Key');
    $vars = tagCache::showAdv("{$this->mod}:$stype,$adfpos$adfcol");
}elseif($pmod=='types'){
    $vars = read($this->mod);
}else{
    $vars = isset($_groups[$this->mod]) ? $_groups[$this->mod] : $this->error("Error-Mod(vlist):{$this->mod}");
}

// deal:vars
// fields,
