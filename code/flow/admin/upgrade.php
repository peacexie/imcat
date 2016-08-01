<?php
(!defined('RUN_MODE')) && die('No Init');
usrPerm::run('pfile','(auto)');

$mod = basReq::val('mod','upvnow'); 
$marr = array( 
	'upvnow' => '升级当前系统',
	'import' => '导入旧版数据',
);
$mtitle = @$marr[$mod];
$links = admPFunc::fileNav($mod,'upd_vers');
glbHtml::tab_bar("[更新升级]<span class='span ph5'>#</span>$mtitle","$links",50);

$tiprows = "
    \n<tr><td class='tc w180'>备份数据库</td>\n<td>
		[建议/注意]先停止掉服务再备份；可复制数据库目录； 
	</td></tr>\n
    \n<tr><td class='tc w180'>备份程序文件 </td>\n<td>
		主要备份：/code 和 /root目录。 
		<br>重要文件（夹）：/code/cfgs/目录，/root/run/_paths.php文件。
	</td></tr>\n";

glbHtml::fmt_head('fmlist',"?",'tblist');
	
if($mod=='upvnow'){
	
	echo "\n<tr><th class='tc'></th>\n<th>升级当前系统：</th></tr>\n";
	echo "\n<tr><td class='tc w180'>使用情景</td>\n<td>
		目前是较旧版本，下载最新版本包(不用安装)，在现有较旧版本上，把新版本增加更新的文件，数据库结构更新过来；
	</td></tr>\n";
    echo $tiprows;
	echo "\n<tr><td class='tc w180'>开始升级</td>\n<td class='tc'>
		<a href='".PATH_ROOT."/tools/setup/upvnow.php' target='_blank' class='f18 fB'>开始升级</a>
	</td></tr>\n";
	

}elseif($mod=='import'){

	echo "\n<tr><th class='tc'></th>\n<th>导入旧版数据：</th></tr>\n";
	echo "\n<tr><td class='tc w180'>使用情景</td>\n<td>
		目前是安装配置好的最新版本，在新版本上，把旧版本数据导入过来；
	</td></tr>\n";
    echo $tiprows;
	echo "\n<tr><td class='tc w180'>开始导入</td>\n<td class='tc'>
		<a href='".PATH_ROOT."/tools/setup/upvimp.php' target='_blank' class='f18 fB'>开始导入</a>
	</td></tr>\n";
		
}

	echo "\n<tr><th class='tc'></th>\n<th>更新升级提示：</th></tr>\n";
	$text = comFiles::get(DIR_CODE."/tpls/dev/d_uplog/upd_readme.txt"); 
	//$text = extMkdown::pdext($text);
	$link = "<a href='{$_cbase['server']['txmao']}/dev.php?uplog' target='_blank'>[官方文档]</a>";
	echo "\n<tr><td class='tc w180'>更新升级说明<br>或查看<br>$link</td>\n<td>
		<textarea cols='' rows='18' style='width:100%'>$text</textarea>
	</td></tr>\n";
	
glbHtml::fmt_end(array("mod|$mod"));


?>
