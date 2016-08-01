<?php
(!defined('RUN_MODE')) && die('No Init');
require(dirname(__FILE__).'/_wex_cfgs.php');

$types = array('test'=>'测试号','chking'=>'未认证','dingyue'=>'订阅号','fuwu'=>'服务号');
$cfg = array(
	'sofields'=>array('keyword','detail','picurl'), //'appid',
	'soorders'=>array('keyword' => 'keyword(降)','keyword-a' => 'keyword(升)','top' => 'top(降)','top-a' => 'top(升)'),
	//'soarea'=>array('amount','数量'),
);
$tabid = 'wex_keyword'; //$weapp

if($view=='list'){ 

	$dop = new dopExtra($tabid,$cfg); 
	$dop->so->whrstr .= " AND `appid`='$weapp'";
	$dop->order = $dop->so->order = basReq::val('order','top-a'); 
	if(!empty($bsend)){
		if(empty($fs_do)) $msg = "请选择操作项目！"; 
		if(empty($fs) && in_array($fs_do,array('delete','clearact'))) $msg = "请勾选操作记录！";
		$cnt = 0; 
		if(empty($msg)){
		  if($fs_do=='delete'){ 
			  foreach($fs as $id=>$v){ 
			  	$cnt += $dop->opDelete($id);
			  } 
		  }
		}
		$cnt && $msg = ($cnt>0) ? "$cnt 条记录 删除成功！" : "操作成功！";
	}
	
	$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
	$links = $cv->Url('添加&gt;&gt;',0,"$aurl[1]&view=form","添加配置"); //$links = admPFunc::fileNav('logs','sms');
	$dop->sobar("公众号[$wekid] : 关键字设置 | $links {$umsg}",40,array(),array('wekid'=>$wekid));
	
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>keyword</th><th>detail</th>"; 
	echo "<th>url</th><th>picurl</th><th>top</th><th>修改</tr>\n";
	$idfirst = ''; $idend = '';
	if($rs=$dop->getRecs()){ 
		foreach($rs as $r){ 
		  $kid = $idend = $r['kid'];
		  if(empty($idfirst)) $idfirst = $kid;
		  $keyword = $r['keyword']; if($keyword=='follow_autoreply_info') $keyword = '(关注)';
		  echo "<tr>\n".$cv->Select($kid);
		  echo "<td class='tc'>$keyword</td>\n";
		  echo "<td class='tc'><input name='fm_[detail]' type='text' value='$r[detail]' class='txt w300' /></td>\n";
		  echo "<td class='tc'><input name='fm_[url]' type='text' value='$r[url]' class='txt w120' /></td>\n";
		  echo "<td class='tc'><input name='fm_[picurl]' type='text' value='$r[picurl]' class='txt w120' /></td>\n";
		  echo "<td class='tc'>$r[top]</td>\n";
		  echo $cv->Url('修改',1,"$aurl[1]&view=form&kid=$kid","修改配置");
		  //echo "<td class='tc'><a href='?file=awex/wex_rkey&kid=$r[kid]' target='_blank'>关键字</a></td>\n";
		  echo "</tr>";
		}
		$dop->pgbar($idfirst,$idend,"delete|*删除关键字");
	}else{
		echo "\n<tr><td class='tc' colspan='15'>无资料！</td></tr>\n";
	}
	glbHtml::fmt_end(array("mod|$mod"));
		
}elseif($view=='form'){
	
	$fm['appid'] = $weapp;
	if(!empty($bsend)){	
		if($kid=='is__add'){
			$kid = $fm['kid'];
			if($db->table($tabid)->where("kid='$kid'")->find()){
				$msg = "该条目[$kid]已被占用！";
			}else{
				$msg = '添加成功！'; //$fm['type'] = 'test';
				$db->table($tabid)->data(basReq::in($fm))->insert();
			}
		}else{
			unset($fm['kid']);
			$db->table($tabid)->data(basReq::in($fm))->where("kid='$kid'")->update();
			$msg = '更新成功！';
		} 
		basMsg::show($msg);	//,'Redir'?file=$file&mod=$mod
	}else{

		echo basJscss::imp('/skin/a_jscss/weixin.js');
		if(!empty($kid)){
			$fm = $db->table($tabid)->where("kid='$kid'")->find();
		}else{
			$def = array('kid'=>'','keyword'=>'','detail'=>'','url'=>'','top'=>'88',);
			foreach($def as $k=>$v){ if(!isset($fm[$k])) $fm[$k] = $v; }		
		}
		$setType = "<label><input type=\"radio\" class=\"radio\" id=\"sence_1\" name='sence_k' onclick='wxKwdsetSence(1)' ".($fm['keyword']==='follow_autoreply_info' ? '' : 'checked').">按信息关键词回复</label> &nbsp; &nbsp; \n"
				."<label><input type=\"radio\" class=\"radio\" id=\"sence_0\" name='sence_k' onclick='wxKwdsetSence(0)' ".($fm['keyword']==='follow_autoreply_info' ? 'checked' : '').">关注时回复</label> \n";
		$itop = " &nbsp; 顺序<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='允许2-5数字' />";
		echo "<div class='h02'>&nbsp;</div>";
		glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
		if(!empty($kid)){
			glbHtml::fmae_row('Key标识',"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$itop");
		}else{
			glbHtml::fmae_row('Key标识',"<input name='fm[kid]' type='text' value='".basKeyid::kidTemp()."' class='txt w150' maxlength='24' reg='str:3-24' />$itop");
		}
		glbHtml::fmae_row('回复场景',"$setType");
		$tip = "最多60个字,可用半角逗号[,]分开; <br>关键词不要重复,不要一个关键词包含另一关键词; <br>关注时回复固定为follow_autoreply_info.";
		glbHtml::fmae_row('关键字',"<input name='fm[keyword]' id='fm[keyword]' type='text' value='$fm[keyword]' ".($fm['keyword']==='follow_autoreply_info' ? 'disabled' : '')." class='txt w320' maxlength='96' reg='str:2-96' tip='$tip' />");		
		glbHtml::fmae_row('内容',"<textarea name='fm[detail]' rows='6' cols='50' wrap='wrap'>$fm[detail]</textarea>");
		glbHtml::fmae_row('Url',"<input name='fm[url]' type='text' value='$fm[url]' class='txt w320' maxlength='96' />");

		glbHtml::fmae_send('bsend','提交','25');
		glbHtml::fmt_end(array("kid|".(empty($kid) ? 'is__add' : $kid)));
		//echo basJscss::jscode('wxKwdsetSence(1,1);');
	}
	
}
?>
