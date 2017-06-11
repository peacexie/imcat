<?php

// extSeo类
 
class extSeo{

    function bdLinks($data=''){
        https://www.baidu.com/s?wd=site%3Atxjia.com&pn=10
    }

    public $db = NULL;
    public $tabid = 'bext_paras';
    
    //function __destory(){  }
    function __construct(){ 
        $this->db = glbDBObj::dbObj();
        //$this->bpushCfg();
    }
    
    // 
    function createSmap($job=''){
        $jcfg = $this->db->table($this->tabid)->where("kid='$job'")->find(); 
        $tfcfg = array(
            'daily'=>'Y-m-d','monthly'=>'Y-m','yearly'=>'Y',
            'always'=>'Y-m-d H:i:s','weekly'=>'Y-m-d',
        ); 
        $a = basElm::line2arr($jcfg['note'],1,';'); $fdata = ''; 
        foreach($a as $tmp){
            if(empty($tmp)) continue;
            $b = explode(',',$tmp); //cargo,100,chn,monthly,0.7
            if(empty($exd)){
                $exd = new exdBase($b[0]);
            }else{
                $exd->minit($b[0]);    
            }
            $cfg = array('limit'=>$b[1],'order'=>$exd->mkid.':DESC'); //stype,limit(1-500),order(did:ASC),offset
            $data = $exd->odata($cfg,0,''); 
            foreach($data as $row){
                $url = vopUrl::fout("{$b[2]}:{$b[0]}.{$row[$exd->mkid]}",0,1); 
                $title = empty($row['title']) ? (empty($row['company']) ? @$row['mname'] : $row['company']) : $row['title'];
                $istr = $jcfg['cfgs']."\n";
                $istr = str_replace(array("(url)","(title)"),array($url,$title),$istr);
                $tfmt = @$tfcfg[$b[3]]; $stamp = empty($row['etime']) ? @$row['atime'] : $row['etime'];
                $time = $stamp ? date($tfmt,$stamp) : date('Y-m-d');
                $istr = str_replace(array("(freq)","(time)","(priority)"),array(@$b[3],$time,@$b[4]),$istr);
                $fdata .= $istr;
            }
        }
        $fdata && $jcfg['detail'] && $fdata = str_replace(array("(*)"),array($fdata),$jcfg['detail']);
        @mkdir(DIR_HTML."/map", 0777, true);
        $file = DIR_HTML."/map/$job"; 
        $res = $fdata ? comFiles::put($file,$fdata) : 0;
        return $res;
    }    
    
    // 
    function bpushCfg(){
        if(!empty($this->pcfg)) return $this->pcfg;
        $list = $this->db->table($this->tabid)->where("pid='seo_push'")->order('top')->select(); 
        $data = array(); $d = array(''=>'','title'=>'','detail'=>'','top'=>'888',); //$n = 0; 
        if($list){ foreach($list as $row){
            $data[$row['kid']] = $row;
        }}
        foreach(array('site','token','time') as $k){
            if(empty($data["push_$k"])){
                $this->db->table($this->tabid)->data(array('kid'=>"push_$k",'pid'=>'seo_push',))->insert();
                $data["push_$k"] = array_merge($d,array('kid'=>"push_$k"));
            }
        }
        $this->pcfg = $data;
        return $this->pcfg;
    }
    
    // 
    function bpushRun($job='baidu_push.txt',$text=''){
        if(empty($text)){
            $file = DIR_HTML."/map/$job";
            $data = comFiles::get($file);
            @$updtime = filemtime($file);    
        }else{
            $data = $text;    
        }  
        $urls = str_replace(array(";","\r","\n\n"),"\n",$data);
        $pcfg = $this->bpushCfg(); 
        if(empty($urls) || empty($this->pcfg['push_token']['detail'])){
            return array('ok'=>0,'msg'=>'Error:Data or API.');    
        }
        $api = "http://data.zz.baidu.com/urls?site=".$this->pcfg['push_site']['detail']."&token=".$this->pcfg['push_token']['detail'];
        // POST
        $header = 'Content-Type:text/plain';
        $restr = comHttp::curlCrawl($api, $urls, 5, $header);
        $rslog = strlen($restr)>180 ? substr($restr,0,150).'...'.substr($restr,strlen($restr)-20) : $restr;
        // 
        @$res = json_decode($restr);
        if(empty($restr) || empty($res)){
            $msg = 'Error:push-Empty';
        }elseif(!empty($res->success)){
            if(empty($text)){
                $this->db->table($this->tabid)->data(array('detail'=>$_SERVER["REQUEST_TIME"]))->where("kid='push_time'")->update();
                // 注意更新时间为生成文件的时间。
                comFiles::put($file,''); //清空旧资料
            }
            $msg = basLang::show('push_ok').$rslog;
        }else{
            $msg = basLang::show('push_ng').$rslog;
        }
        basDebug::bugLogs('bpushRun',$msg,'detmp','db');
        $nok = empty($res->success) ? 0 : $res->success;
        return array('nok'=>$nok,'msg'=>$msg);    
    }
    // 
    function bpushSql(){
        $ptime = empty($this->pcfg['push_time']['detail']) ? 0 : $this->pcfg['push_time']['detail'];
        $stamp = $_SERVER["REQUEST_TIME"]; //当前时刻
        $sbase = strtotime('2005-12-31'); //更新一个含早的时间:2000年,肯定还没有这些功能...
        if(empty($ptime)){
            $sql = " AND atime<='$stamp'"; 
        }else{
            $sql = " AND atime>='$ptime'"; //当前到上次推送时间
        }
        return $sql;
    }

}

/*

http://zhanzhang.baidu.com/linksubmit/index
http://data.zz.baidu.com/urls?site=www.example.com&token=9IFIpTzdC8rFs1vT

*/

