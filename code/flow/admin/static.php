<?php
(!defined('RUN_MODE')) && die('No Init');
usrPerm::run('pfile','(auto)'); 

//global $_cbase;
$ntpl = basReq::val('ntpl',$_cbase['tpl']['def_static']);
$_cbase['tpl']['tpl_dir'] = $ntpl;
$cronurl = PATH_ROOT."/plus/ajax/cron.php";

$view = basReq::val('view','list');
$nmod = basReq::val('nmod','home'); 
$vcfgs = vopTpls::etr1('tpl'); 
$stitle = "静态管理:($ntpl){$vcfgs[$ntpl][0]}"; 
$msg = ''; //print_r($msg);

$lnks = "# "; $ncfg = array(); 
foreach($vcfgs as $itpl=>$suit){
	if(strpos($_cbase['tpl']['no_static'],$itpl)) continue;
	if($itpl==$ntpl){ // dynamic/static/both/all/
		$ncfg = vopTpls::entry($itpl,'ehlist','static');  
	}
	$ititle = $itpl==$ntpl ? "<span class='cF0F'>$suit[0]<span>" : $suit[0];
	$lnks .= "<a href='?file=$file&ntpl=$itpl'>$ititle</a> # ";
    
}
glbHtml::tab_bar("$stitle $msg",$lnks,40); 

if($view=='list'){

	$mods = array_keys($ncfg);
	glbHtml::fmt_head('fmlist',"?",'tblist');
	echo "\n<tr><th class='tc'></th>\n<th>静态管理 --- $ntpl:$nmod --- <a href='?file=$file&ntpl=$ntpl&nmod=all'>全部模块</a></th></tr>\n";
	echo "\n<tr><td class='tc'>操作模块：</td>\n<td>"; $ti = 0;
	foreach($mods as $imod){
		$iname = $imod=='home' ? '首页' : (isset($_groups[$imod]) ? $_groups[$imod]['title'] : "($imod)");
		$ititle = $imod==$nmod ? "<span class='cF0F'>$iname<span>" : "$iname";
        if($ti==0) echo " ";
        else echo ($ti && $ti%6==0) ? "<br>" : " # ";
		echo "<a href='?file=$file&ntpl=$ntpl&nmod=$imod'>$ititle</a>";	
        $ti++;
	}
	echo "</td></tr>\n";
    if($nmod=='home'){
		$sfile = vopStatic::getPath('home','home',0);
        $exists = file_exists(DIR_HTML."/$sfile") ? date('Y-m-d H:i:s',filemtime(DIR_HTML."/$sfile")) : '不存在';
        echo "\n<tr><td class='tc'>首页静态</td>\n<td>
			静态文件：{html}".$sfile." (".$exists.")
            <p class='tc f18'>
            <a href='$cronurl?static=home&tpldir=$ntpl&act=add' class='f18 fB' onclick='return winOpen(this);'>生成静态</a> #
            <a href='$cronurl?static=home&tpldir=$ntpl&act=del' class='f18 fB' onclick='return winOpen(this);'>删除静态</a> # 
            <a href='".vopUrl::ftpl("$ntpl:0")."' target='_blank' class='f18 fB' target='_blank'>查看效果</a>
            </p>
		</td></tr>\n";
	}elseif($nmod!=='all'){  
		$iname = isset($_groups[$nmod]) ? $_groups[$nmod]['title'] : "(自定义)";
		$mcfgs = glbConfig::read($nmod); 
        echo "\n<tr><td class='tc'>{$iname}<br>[$ntpl:$nmod]</td>\n<td>";
		echo "\n<p>
            &nbsp; ● 栏目列表静态：共约：[".count($ncfg[$nmod])."] 条
            </p>
            <p class='tc f14'>
            <a href='$cronurl?static=mlist&mod=$nmod&tpldir=$ntpl&act=add' class='fB' onclick='return winOpen(this);'>生成静态</a> #
            <a href='$cronurl?static=mlist&mod=$nmod&tpldir=$ntpl&act=del' class='fB' onclick='return winOpen(this);'>删除静态</a> 
            </p>";
		if(!empty($mcfgs['pid']) && in_array($mcfgs['pid'],array('docs','users'))){
		echo "\n<p class='right ph20'><a href='".vopUrl::ftpl("$ntpl:0")."?$nmod' target='_blank' class='f18 fB'>模块首页（动态）</a></p>
			<p>
            &nbsp; ● 内容详情静态：共约：[".$db->table("{$mcfgs['pid']}_$nmod")->count()."] 条 <br>
			<input name='limit' type='text' value='20' class='w40' maxlength='3'>条/批次 &nbsp; 
			offset/dirfix：<input name='offset' type='text' value='' class='w40' maxlength='4'> &nbsp; 
            </p>
            <p class='tc f14'>
            <a href='$cronurl?static=mdetail&mod=$nmod&tpldir=$ntpl&act=add' class='fB' onclick='return stsetLink(this);'>生成静态</a> #
            <a href='$cronurl?static=mdetail&mod=$nmod&tpldir=$ntpl&act=del' class='fB' onclick='return stsetLink(this);'>删除静态</a> 
            </p>";
		}
		echo "\n</td></tr>\n";
	}
	glbHtml::fmt_end(array("nmod|$nmod","ntpl|$ntpl"));
    
	if($nmod=='all'){
		echo "<table width='100%' border=1>";
		$i = 0;
		foreach($mods as $imod){
			$i++;
			if($imod=='home'){
				$url = "static=home&tpldir=$ntpl&act=add";
			}else{
				$url = "static=mlist&mod=$imod&tpldir=$ntpl&act=add";	
			}
			echo "\n<td><iframe src='$cronurl?$url' width='100%'></iframe></td>";
			if($i%3==0){ echo "</tr><tr>"; }
			$mcfgs = glbConfig::read($imod); 
			if(!empty($mcfgs['pid']) && in_array($mcfgs['pid'],array('docs','users'))){
				$url = "static=mdetail&mod=$imod&tpldir=$ntpl&act=add";
				echo "\n<td><iframe src='$cronurl?$url' width='100%'></iframe></td>";
				$i++;
			}
			if($i%3==0){ echo "</tr><tr>"; } 
		}
		echo "</table>";
	}
	
	/*
    echo "<pre>"; 
    #$res = vopStatic::updKid('news','2015-9g-mvp1','upd'); print_r($res);
    print_r($mods);
	//*/
    
}

/*	

*/

?>
