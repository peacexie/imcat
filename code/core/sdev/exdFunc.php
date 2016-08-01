<?php
(!defined('RUN_MODE')) && die('No Init');

// ...类exdFunc
class exdFunc extends exdBase{	
	
	public $odb = NULL;
	public $jcfg = array();
	public $cfields = array();
	
	function __construct($mod){ 
		parent::__construct($mod);
	}
	
	// 拉取
	function exdPull(){
		$data = $this->odata();
		//$ret = basReq::val('ret','json');
		$data = comParse::jsonEncode($data);
		return $data;
	}
	
	// 分享
	function exdShow(){ //tpl,cut,clen,ret(html/js),
		global $_cbase;
		$data = $this->odata(); 
		$tpl = basReq::val('tpl','','');
		$burl = vopTpls::etr1('chn',0).'?'; 
		$tpl = $tpl ? comParse::urlBase64($tpl,1) : "<li><a href='{rhome}$burl?$this->mod.{kid}'>{title}</a></li>";
		$tpl = str_replace('{rhome}',$_cbase['run']['rsite'],$tpl);
		$cut = ','.basReq::val('cut',"title,company").',';
		$clen = basReq::val('clen',"255",'N');
		$ret = basReq::val('ret','js'); 
		$str = ''; 
		foreach($data as $nv){ 
			$istr = $tpl;
			foreach($nv as $ik=>$iv){ 
				if(strpos($cut,$ik)) $iv = basStr::cutWidth($iv, $clen);
				$istr = str_replace('{'.$ik.'}',$iv,$istr);	
			}
			$istr = str_replace("{kid}",$nv[$this->mkid],$istr);
			$str .= "$istr\n";
		}
		if($ret=='js'){
			$str = basJscss::jsShow($str, 0);
		}
		return $str;
	}

	// 同步
	function exdPsyn($jcfg=array()){ 
		$this->jcfg = $jcfg;
		$re = array('msg'=>'','cnt'=>0,'ok'=>0,'next'=>'','min'=>'','max'=>'',);
		$data = $this->exdPsyn_Data($jcfg);
		if(!empty($data)){
			foreach($data as $row){ 
				$oid = $row[$this->mkid];
				if(empty($re['min']) || $oid<$re['min']) $re['min'] = $oid;
				if(empty($re['max']) || $oid>$re['max']) $re['max'] = $oid;
				$kar = $this->getJKid($oid); 
				$kid = $kar[0]; $kno = $kar[1];
				$row[$this->mkid] = $kid;
				$row[$this->mkno] = $kno;
				$ire = $this->save($row);
				$ire && $re['ok']++; 
				$dlog = array('kid'=>$jcfg['kid'],'kno'=>intval($ire),'sysid'=>$kid,'outid'=>$oid,);
				$this->db->table('exd_pslog')->data($dlog)->insert(); 
				$re['cnt']++;
			}
			$data = $this->exdPsyn_Data($jcfg,$re['max'],1);
			$re['next'] = empty($data) ? 0 : 1; 
		}else{
			$re['msg'] = '无原始数据';
			$re['next'] = 0;	
		} 
		return $re; 
	}
	function exdPsyn_Data($jcfg=array(),$offset='',$limit=0,$omode='ASC'){ // mod,stype,limit(1-500),order(did:ASC),offset,ret(json)
		if(empty($offset)) $offset = $this->getJFm2('exd_pslog',$jcfg['kid'],'outid','MAX'); 
		$limit = $limit ? $limit : $jcfg['limit'];
		$para = "act=pull&mod=$jcfg[mod]&stype=$jcfg[stype]&limit=$limit&order={$this->mkid}:$omode&offset=$offset";
		$url = $jcfg['api']."/plus/ajax/exdb.php?$para&".exdBase::getJSign();
		$json = comHttp::doGet($url); //var_dump($json);
		$data = comParse::jsonDecode($json); //print_r($data);
		return $data;
	}
	// Update
	function exdPsyn_Update($jcfg=array(),$sysid){ 
		$re = array('msg'=>'','cnt'=>1,'ok'=>0,'next'=>'','min'=>'','max'=>'',);
		$jrow = $db->table("exd_pslog")->where("kid='$jcfg[kid]' AND sysid='$sysid'")->find();
		$data = $this->exdPsyn_Data($jcfg,$jrow['outid'],1,'EQ');
		$ire = $this->supd($data[0]);
		$ire && $re['ok']++; 
	}
	
	// 导入
	function exdOimp($jcfg=array()){
		$this->jcfg = $jcfg;
		$cfields = $this->getJFlds($jcfg['kid']);
		$re = array('msg'=>'','cnt'=>0,'ok'=>0,'next'=>'','min'=>'','max'=>'',);
		$data = $this->exdOimp_Data($jcfg);
		if(!empty($data)){
			foreach($data as $row){ 
				$drow = exdCrawl::ugetRow($jcfg,$cfields,$row);
				$oid = $row[$jcfg['kname']];
				if(empty($re['min']) || $oid<$re['min']) $re['min'] = $oid;
				if(empty($re['max']) || $oid>$re['max']) $re['max'] = $oid;
				$defid = basKeyid::kidTemp('hms',empty($jcfg['ktime']) ? '' : $row[$jcfg['ktime']]);
				$kar = $this->getJKid($defid); 
				$kid = $kar[0]; $kno = $kar[1];
				$drow[$this->mkid] = $kid;
				$drow[$this->mkno] = $kno;
				$ire = $this->save($drow);
				$ire && $re['ok']++;
				$oukey = $jcfg['ktype']=='int' ? 'ouint' : 'outid';
				$dlog = array('kid'=>$jcfg['kid'],'kno'=>intval($ire),'sysid'=>$kid,$oukey=>$oid,);
				$this->db->table('exd_oilog')->data($dlog)->insert(); 
				$re['cnt']++;
			}
			$data = $this->exdOimp_Data($jcfg,$re['max'],1);
			$re['next'] = empty($data) ? 0 : 1; 
		}else{
			$re['msg'] = '无原始数据';
			$re['next'] = 0;	
		} 
		return $re; 
	}
	function exdOimp_Data($jcfg=array(),$offset='',$limit=0,$omode='>'){ 
		// id=9ac2e7b1f1d22e9e57260f6553822520,order:oid, outid|ouint
		$oukey = $jcfg['ktype']=='int' ? 'ouint' : 'outid';
		if(empty($offset)) $offset = $this->getJFm2('exd_oilog',$jcfg['kid'],$oukey,'MAX'); 
		if(empty($this->odb)){
			$ocfgs = glbConfig::read('outdb','ex');
			require_once(DIR_CODE."/adpt/dbdrv/db_pdox.php");
			$this->odb = new db_pdox(); 
			$this->odb->connect($ocfgs[$jcfg['odb']]); 
		}
		$sql = $jcfg['osql'];
		if($offset){
			$sql = str_replace(array('1=1'),array("m.$jcfg[kname]{$omode}'$offset'"),$sql);	
		} 
		$data = $this->odb->query($sql,'all'); //print_r($data);
		return $data;
	}
	// Update
	function exdOimp_Update($jcfg=array(),$sysid){ 
		$this->jcfg = $jcfg;
		$this->fmv[$this->mkid] = $sysid;
		$re = array('msg'=>'','cnt'=>1,'ok'=>0,'next'=>'','min'=>'','max'=>'',);
		$jrow = $this->db->table("exd_oilog")->where("kid='$jcfg[kid]' AND sysid='$sysid'")->find();
		$oukey = $jcfg['ktype']=='int' ? 'ouint' : 'outid';
		$data = $this->exdOimp_Data($jcfg,$jrow[$oukey],1,'=');
		$cfields = $this->getJFlds($jcfg['kid']);
		$drow = exdCrawl::ugetRow($jcfg,$cfields,$data[0]);
		$ire = $this->supd($drow);
		$re['ok']++; 
		return $re;
	}
	
	// 采集
	function exdCrawl($jcfg=array()){
		$this->jcfg = $jcfg;
		$cfields = $this->getJFlds($jcfg['kid']);
		$re = array('msg'=>'','cnt'=>0,'ok'=>0,'next'=>'','min'=>'','max'=>'',);
		$pm2 = exdCrawl::ugetPages($jcfg);
		$re['next'] = $pm2[1]; //print_r($pm2);
		if(strlen($pm2[0])){
			$urls = exdCrawl::ugetLinks($jcfg,$pm2[0]);
			foreach($urls as $url){
				$omd5 = md5($url);
				if(!$this->db->table("exd_crlog")->where("kid='{$jcfg['kid']}' AND omd5='$omd5'")->find()){
					$re['ok']++;
					$drow = exdCrawl::ugetRow($jcfg,$cfields,$url);
					$defid = basKeyid::kidTemp('hms',empty($drow['atime']) ? '' : $drow['atime']);
					$kar = $this->getJKid($defid); 
					$kid = $kar[0]; $kno = $kar[1]; 
					$drow[$this->mkid] = $kid;
					$drow[$this->mkno] = $kno;
					$ire = $this->save($drow);
					$dlog = array('kid'=>$jcfg['kid'],'kno'=>intval($ire),'sysid'=>$kid,'outurl'=>$url,'omd5'=>$omd5,);
					$this->db->table('exd_crlog')->data($dlog)->insert();
				}
				$re['cnt']++; 
			}
			$oplog = str_replace(",$pm2[0],",',',$jcfg['oplog']);
			$oplog = str_replace(array(",,"),array(","),"$oplog,$pm2[0],");
			$this->db->table('exd_crawl')->data(array('oplog'=>$oplog))->where("kid='{$jcfg['kid']}'")->update();
		}else{
			$re['msg'] = '无原始数据';
			$re['next'] = 0;
		}
		return $re; 
	}
	// Debug
	function exdCrawl_Debug($jcfg=array(),$debug='links'){ 
		$cfields = $this->getJFlds($jcfg['kid']); 
		if($debug=='links'){ 
			return exdCrawl::ugetLinks($jcfg);
		}elseif($debug=='field'){
			$url = basReq::val('url');
			$url || $url = $jcfg['odmp'];
			$data = comHttp::doGet($url,5); 
			$data = comConvert::autoCSet($data,$jcfg['ocset'],'utf-8'); // echo $data;
			$field = basReq::val('field');
			return exdCrawl::orgAll($data,$cfields[$field]);
		}
	}
	// Update
	function exdCrawl_Update($jcfg=array(),$sysid){ 
		$this->jcfg = $jcfg;
		$this->fmv[$this->mkid] = $sysid;
		$re = array('msg'=>'','cnt'=>1,'ok'=>0,'next'=>'','min'=>'','max'=>'',);
		$jrow = $this->db->table("exd_crlog")->where("kid='$jcfg[kid]' AND sysid='$sysid'")->find();
		$cfields = $this->getJFlds($jcfg['kid']);
		$drow = exdCrawl::ugetRow($jcfg,$cfields,$jrow['outurl']);
		$ire = $this->supd($drow);
		$re['ok']++; 
		return $re;
	}
	
}

