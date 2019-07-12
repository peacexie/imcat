<?php
namespace imcat;

# 所有版本公用方法

class baseApi{
    
    public $cfgs = []; // mkv,mksp,mod,key,func
    public $row = [];

    //function __destory(){  }
    function __construct($cfgs){
        $this->cfgs = $cfgs;
        if($this->cfgs['mksp']=='.'){
            $this->_init(); 
        }
    }

    function _init(){
        extract($this->cfgs);
        $this->row = glbData::getRow($mod, $key);
        if(empty($this->row)){ vopApi::verr("Error `$key`!"); }
    }

    // 公用数据块
    # function topnNews($top=4)

    // 公用act

    function _list($mod='news', $exwhr=''){
        $res['parts'] = $this->_tab($mod);
        $whr = "";
        $stype = req('stype');
        if($stype){
            if(in_array($pid,array('docs','advs'))){
                $sql = basSql::whrTree($this->mcfg['i'],'m.catid',$stype);
                $whr .= $sql ? $sql : '';
            }else{
                $whr .= " AND catid='$stype'";
            }
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

    function xxxAct(){
        return $this->error('Test Error.');
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
