<?php
namespace imcat;

# 所有版本公用方法

class baseApi{
    
    public $cfgs = []; // mkv,mksp,mod,key,func
    public $row = [];
    public $uver = [
        'key'=>'201', 'val'=>'2.0.1',
        'url'=>'http://imcat.txjia.com/h5/down',
    ]; // 最新版本,用于更新
    public $skips = ['info'];

    //function __destory(){  }
    function __construct($cfgs){
        $this->cfgs = $cfgs;
        if($this->cfgs['mksp']=='.' && !in_array($cfgs['mod'],$this->skips)){
            $this->_init(); 
        }
        #$msg = $_SERVER;
        #$msg['_ip'] = basEnv::userIP();
        #basDebug::bugLogs("tmp-baseApi", $msg, "tmp-baseApi.log", 'file');
        //usleep(200000);
        //sleep(1);
    }

    function _init(){
        extract($this->cfgs);
        $this->row = glbData::getRow($mod, $key);
        if(empty($this->row)){ vopSapi::verr("Error `$key`!"); }
    }

    // 公用数据块
    # function topnNews($top=4)

    // 公用act

    function _list($mod='news', $exwhr=''){
        $res['parts'] = $this->_tab($mod);
        $whr = "";
        $stype = req('stype');
        if($stype){
            $sql = basSql::whrTree(read("$mod.i"),'catid',$stype);
            $whr .= $sql ? $sql : ''; 
        }        
        $keyword = req('keyword');
        $keyword && $whr .= " AND title='$keyword'";
        $exwhr && $whr .= $exwhr;
        $psize = 20;
        $page = req('page', 1, 'N'); //dump($whr);
        $limit = max(($page-1),0) * $psize . ',' . $psize;
        $res['list'] = data($mod, $whr, $limit, 'atime');
        $res['recs'] = data($mod, $whr, 'count');
        return $res;
    }

    function _tab($mod='news'){
        $tab = read("$mod.i");
        $res = []; 
        foreach($tab as $no=>$row){
            $res[] = $row;
        }
        return $res;
    }  

}

/*
    get($mod, $whr='', $lmt='10', $ord='', $ops=[]);
    $mod: news, news.join
    $whr: `show`='all', `show`='0', 
    $lmt: 1, 10, 3,10, 10.page, count
    $ord: atime-1, atime
    $ops: xxx
*/
