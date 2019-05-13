<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

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
        $burl = vopTpls::etr1(1,'comm'); 
        $tpl = $tpl ? comParse::urlBase64($tpl,1) : "<li><a href='$burl?$this->mod.{kid}'>{title}</a></li>";
        $tpl = str_replace('{rmain}',$_cbase['run']['rmain'],$tpl);
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
    
    // 采集
    function exdCrawl($jcfg=array()){
        $this->jcfg = $jcfg;
        $cfields = $this->getJFlds($jcfg['kid']);
        $re = array('msg'=>'','cnt'=>0,'ok'=>0,'next'=>'','min'=>'','max'=>'',);
        $pm2 = exdCrawl::ugetPages($jcfg);
        $re['next'] = $pm2[1]; 
        if(strlen($pm2[0])){
            $urls = exdCrawl::ugetLinks($jcfg,$pm2[0]);
            foreach($urls as $url){
                $omd5 = md5($url);
                if(!$this->db->table("exd_crlog")->where("kid='{$jcfg['kid']}' AND omd5='$omd5'")->find()){
                    $re['ok']++;
                    $drow = exdCrawl::ugetRow($jcfg,$cfields,$url);
                    $defid = basKeyid::kidTemp('',empty($drow['atime']) ? '' : $drow['atime']);
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
            $re['msg'] = basLang::show('core.nul_orgdata');
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
            $data = comConvert::autoCSet($data,$jcfg['ocset'],'utf-8'); 
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

