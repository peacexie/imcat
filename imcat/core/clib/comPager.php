<?php 
namespace imcat;

// 分页类
// 优化1: 多页提交，不重复计算记录数目
// 优化2: ORDER BY 主键，不用 OFFSET
class comPager{    
    // sql
    public  $sfrom = '';
    public  $where = '';
    // page
    //public  $paras = '?';
    public  $psize = 20; //分页大小
    public  $page = 1; //当前页
    public  $pcnt = 1; //总前页
    // page para
    public  $prec = 0; //总记录数
    public  $ptype = 'start'; //first,prev,next,last
    public  $pkey = ''; 
    // order    
    public  $order = ''; //排序字段,含前缀,如a.aid
    public  $orderb = ''; //排序字段,去掉了前缀,如aid
    public  $odesc = true;
    public  $opkey = false; 
    
    public  $rs = array();
    public  $bar = array();
    public  $sql = array('','');

    //function __destory(){  basDebug::bugLogs('page'); }
    function __construct($sfrom,$where,$psize=0,$order=''){ 
        $this->sfrom = $sfrom;
        $this->where = $where;
        $this->psize = $psize;
        $this->order = $order;
        if(strpos($order,'.')){
            $this->orderb = substr($order,strpos($order,'.')+1);
        }else{
            $this->orderb = $order;    
        }
        // init;
        $a = array('page','prec','ptype','pkey',); // 'odesc','opkey',
        foreach($a as $k){
            if(isset($_GET[$k])){ 
                $__v = basReq::val($k,'Key',24); 
                if(in_array($k,array('page',))) $__v = max(1,intval($__v));
                if(in_array($k,array('prec',))) $__v = max(0,intval($__v));
                $this->$k = $__v;
            }
        }
        if(''!==$om=basReq::val('odesc','N',1)){ $this->odesc = $om; }
    }
    
    function set($key,$value=0){
        if(is_array($key)){
            foreach($key as $k=>$v) $this->set($k,$v);
        }else{
            $this->$key = $value;
        } 
    }
    
    function sql($cnt=''){ 
        $sfrom = ' SELECT '.$this->sfrom;
        if($cnt){
            $where = $this->where ? ' WHERE '.$this->where : '';
            $this->sql[1] = basSql::fmtCount($sfrom.$where,$cnt);
            return $this->sql[1];
        }else{
            $where = ' WHERE '.($this->where ? $this->where : '1=1');
            $ptype = $this->ptype;
            $pkey = $this->pkey; 
            $odesc = $this->odesc;
            if($this->opkey&&$ptype){
                if($ptype=='start'){
                    //$where .= "";
                }else if($ptype=='end'){
                    //$where .= "";
                }else if($ptype=='next'){ 
                    if($odesc) $where .= " AND {$this->order}<'$pkey'";
                    if(!$odesc) $where .= " AND {$this->order}>'$pkey'";
                }else if($ptype=='prev'){
                    if($odesc) $where .= " AND {$this->order}>'$pkey'";
                    if(!$odesc) $where .= " AND {$this->order}<'$pkey'";
                } 
                if($ptype=='end'){
                    $lcnt = $this->prec%$this->psize;
                    $lcnt = $lcnt ? $lcnt : $this->psize;
                    $limit = ' LIMIT '.$lcnt; 
                }else{
                    $limit = ' LIMIT '.$this->psize;
                }
            }else{
                $offset = ($this->page-1)*$this->psize;
                $limit = " LIMIT $offset,".$this->psize;
            }
            if($where==' WHERE 1=1') $where = "";
            if($ptype=='end'||$ptype=='prev'){
                $ord_in = ' ORDER BY '.$this->order.($this->odesc ? '' : ' DESC');
                $ord_out = ' ORDER BY '.$this->orderb.($this->odesc ? ' DESC' : '');
                $this->sql[0] = "SELECT * FROM ($sfrom $where $ord_in $limit) _tab__ $ord_out ";
            }else{
                $order = ' ORDER BY '.$this->order.($this->odesc ? ' DESC' : '');
                $this->sql[0] = $sfrom.$where.$order.$limit;
            }
            return $this->sql[0];
        }
    }
    function exe($dbkey=''){
        $db = glbDBObj::dbObj($dbkey);
        $rs = $db->query($this->sql());
        if(!$this->prec){ 
            $rec = $db->query($this->sql('_rc_recs_'));
            $this->prec = $rec[0]['_rc_recs_'];
        } 
        $this->pcnt = ceil($this->prec/$this->psize);
        $this->bar = $this->links();
        return $rs;
    }
    
    function show($kfirst='',$klast='',$type='',$pbase=''){
        $pbase = empty($pbase) ? basReq::getUri(-1,'','page|prec|ptype|pkey') : $pbase; 
        $a = $this->bar; $bar = ''; $para = "&prec=$this->prec&page=";
        $p0 = array("{pfirst}","{pprev}","{pnext}","{plast}",); 
        $p1 = array("{$para}1","$para".($this->page-1),"$para".($this->page+1),"$para".$this->pcnt,); 
        if($this->opkey){
            $p1[0] .= "&ptype=start";
            $p1[1] .= "&ptype=prev&pkey=$kfirst";
            $p1[2] .= "&ptype=next&pkey=$klast";
            $p1[3] .= "&ptype=end";
        }
        $a['pjump'] = str_replace("{pjump}","{$para}0&ptype=0",$a['pjump']); 
        foreach($a as $k=>$v){
            $v = str_replace("<li>","\n<li class='pg_$k'>",$v);
            if(in_array($k,array('first','prev','next','last'))){
                $v = str_replace($p0,$p1,$v);
            }
            $bar .= $v; 
        }
        $bar = str_replace(array("{url}",'.php&'), array($pbase,'.php?'), $bar);
        return "<ul class='pagination'>$bar</ul>";
    }
    
    function links(){
        $pcnt = intval($this->pcnt);
        $a = array(); 
        $sFirst = '<span class="fa fa-fast-backward"></span>';
        $sPrev = '<span aria-hidden="true">&laquo;</span>';
        $sNext = '<span aria-hidden="true">&raquo;</span>';
        $sLast = '<span class="fa fa-fast-forward"></span>';
        
        $a['pagno'] = "<li class='pg_pagno'><a class='disabled'>$this->page/$pcnt</a></li>";
        $a['first'] = "<li><a class='disabled'>$sFirst</a></li>";
        $a['prev']  = "<li><a class='disabled'>$sPrev</a></li>";
        $a['pjump'] = "<li class='pg_pjump'><input type='text' id='pg_pjump' pjurl='{url}{pjump}' pjmax='{$pcnt}' value='$this->page' maxlength='9' class='form-control' onchange=\"goPjump(this);\"/></li>";
        $a['next']  = "<li><a class='disabled'>$sNext</a></li>";
        $a['last']  = "<li><a class='disabled'>$sLast</a></li>";
        $a['total'] = "<li class='pg_total'><a class='disabled'>$this->prec</a></li>";
        if($pcnt<=1) return $a;
        if($this->page==$pcnt){
            $a['first'] = "<li><a href='{url}{pfirst}'>$sFirst</a></li>";
            $a['prev']  = "<li><a href='{url}{pprev}' >$sPrev</a></li>";
        }elseif($this->page==1){
            $a['next']  = "<li><a href='{url}{pnext}' >$sNext</a></li>";
            $a['last']  = "<li><a href='{url}{plast}' >$sLast</a></li>";
        }else{
            $a['first'] = "<li><a href='{url}{pfirst}'>$sFirst</a></li>";
            $a['prev']  = "<li><a href='{url}{pprev}' >$sPrev</a></li>";
            //now
            $a['next']  = "<li><a href='{url}{pnext}' >$sNext</a></li>";
            $a['last']  = "<li><a href='{url}{plast}' >$sLast</a></li>";
        }
        return $a;
    }

    static function fixUrl($key='home'){
        global $_cbase;
        $bar = &$_cbase['page']['bar'];
        if(!strpos($bar,"?$key&")){
            $bar = str_replace('?',"?$key&",$bar);
        }
        return $bar;
    }
    
}
