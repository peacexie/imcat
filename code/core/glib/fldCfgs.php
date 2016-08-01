<?php

class fldCfgs{

	// 字段类型
	static function viewTypes(){
		return array(
			'^group^data'  => '-数据字段-',
			'input'  => '输入框',
			'select' => '下拉选择',
			'cbox'   => '多选框',
			'radio'  => '单选按钮',
			'text'  => '文本框',
			'file'  => '文件域',
			'passwd' => '密码域',
			'hidden' => '隐藏域',
			'^group^control'  => '-控制字段-',
			'parts'  => '表单分段',
			'repeat' => '重复检查',
		);	
	}
	
	// 字段插件
	static function viewPlugs(){
		return array(
			'editor' => 'Html编辑器',
			'pics'   => 'Pics文件组', //图集等
			'pick'   => 'Pick资料选取',
			'winpop' => '弹窗选择',
			'datetm' => '日期选择',
			'color'  => '颜色设置',
			'map'	=> '地图标点',
		);		
	}

	// 数据类型
	static function dbTypes(){
		return array(
			'varchar' => 'varchar.字符',
			'int'	 => 'int.整型',
			'float'   => 'float.浮点',
			'text'	=> 'text.64K长文本',
			'mediumtext' => 'mtext.16M长文本',
			'nodb'	=> 'nodb.不保存',	
		);		
	}
	
	// 字段认证
	static function regTypes(){
		return array(
			'fix:tel'   => '电话号码',
			'fix:email' => '邮件地址',
			'fix:uri'   => 'Url地址',
			'fix:file'  => '文件',
			'fix:image' => '图片',
			'key:'	  => 'Key(变量形式)',
			'fix:xid'   => 'Key(9999-md)',
			'tit:'	  => '标题',
			'n+i'	   => '正整数',
			'n-i'	   => '整数(含负)',
			'n+d'	   => '小数',
			'n-d'	   => '小数(含负)',
			'str:'	  => '普通文本',
			'vimg:'	 => '认证码',		
		);		
	}
	
	// 添加参数
	static function addParas(){ 
		return array('type','fmextra','etab','kid','from');
	}
	
	
	
	// -------------------------------------
	
	// 保留字段
	static function setKeeps($key=''){
		$arr_0 = array('info','cfgs','fields','items','catalog','files');
		$arr_n = array_keys(glbConfig::read('fsystem','sy'));
		$arr_f = glbConfig::read('fkeywd','sy'); 
		return array_merge($arr_0,$arr_n,$arr_f);
	}
	
	// ==================================================================== 
	
	/* 'ename'=>array('title'=>'英文名','etab'=>'0','type'=>'input','enable'=>'0','vmax'=>'96',
		   'vreg'=>'str:2-60','vtip'=>'标题2-60字符','dbtype'=>'varchar','dblen'=>'96','dbdef'=>NULL,),
	// mymap|map|地图^varchar|255|-|str:|2|255 // nul:fix:image // tit:2-60 */
	static function addPick($mod,$re='str'){
		$_groups = glbConfig::read('groups'); 
		$db = glbDBObj::dbObj();
		$mpid = $_groups[$mod]['pid']; //echo $mpid;
		$ademo = self::addDemo('init_docs')+self::addDemo('init_dext')+self::addDemo('init_coms')+self::addDemo('init_users'); 
		$list = $db->table('base_fields')->where("model='$mod'")->select();
		$amods = array(-1); $b = array(); $s = ' &nbsp; 参考类别'; $a = array();
		if($list) foreach($list as $r) $amods[] = $r['kid'];
		foreach($_groups as $k=>$v){ 
		if($v['pid']=='types'){
			if(in_array("type_$k",$amods)) continue;
			$data = "$k|input|winpop|0";
			$s .= " | <a href='#' onclick=\"gf_setDemoField('$data')\" class='span'>$v[title]</a>\r\n";
		}}
		$s .= "<br> &nbsp; 参考字段";
		foreach($ademo as $k=>$v){
			if(in_array($k,$amods)) continue;
			if(!in_array($k,$a)){
				$data = "$k|$v[type]|".@$v['fmextra']."|".@$v['etab'].""; 
				$s .= " | <a href='#' onclick=\"gf_setDemoField('$data')\" class='span'>$v[title]</a>\r\n";
				$a[] = $k;
			}
		}
		return $s;
		//return $re=='str' ? $s : $a;
	}
	// 'exp_t01'=>'扩展参数-text-1', //<a href='#' onclick="gf_setDemoField('t400|input||0')" class='span'>XXXX</a>
	static function addType($mod,$catid){
		$ccfg = glbConfig::read($mod,'_c');
		$flist = glbConfig::read('fsystem','sy'); 
		$s = ' &nbsp; 参考字段';
		foreach($flist as $k=>$v){
			if(strstr($k,'exp_')){
				$a = explode('-',$v);
				$type = str_replace("checkbox","cbox",$a[1]);
				$data = "$k|$type||0";
				if(isset($ccfg[$catid][$k])){
					$s .= " | <i class='span'>$v</i>\r\n";
				}else{
					$s .= " | <a href='#' onclick=\"gf_setDemoField('$data')\" class='span'>$v</a>\r\n";
				}
			}
		}
		return $s;
	}
	static function addDemo($mod){
		$tmp = glbConfig::read('fdemo','sy');
		return $tmp[$mod];
	
	}
	
	static function getSizeArray($cfg=array()){
		if(empty($cfg['fmsize'])){
			$size = array();
		}elseif(strpos($cfg['fmsize'],'.')){ //news.8
			$size = explode('.',$cfg['fmsize']);
		}else{ //360x8
			$size = explode('x',$cfg['fmsize'].'x');	
		}
		return $size;
	}
	
}