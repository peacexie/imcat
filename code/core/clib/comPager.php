<?php 

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
	// page para
	public  $prec = 0; //总记录数
	public  $ptype = 'start'; //first,prev,next,last
	public  $pkey = ''; 
	public  $pjump = 0; //jump
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
		$a = array('page','prec','pjump','ptype','pkey',); //'odesc','opkey',
		foreach($a as $k){
			if(!empty($_GET[$k])){ 
				$__v = basReq::val($k,'Key',24); //req($k); 
				if(in_array($k,array('page','prec','pjump',))) $__v = max(0,intval($__v));
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
		} // if(in_array($var,get_object_vars($this)))
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
					$where .= "";	
				}else if($ptype=='end'){
					$where .= "";
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
					$limit = ' LIMIT '.$lcnt; //echo "@@@$limit";
				}else{
					$limit = ' LIMIT '.$this->psize;
				}
				if($this->pjump) $limit = ' OFFSET '.($this->pjump*$this->psize);
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
			} //echo "<br>{$this->sql[0]}";
			return $this->sql[0];
		}
	}
	function exe(){
		$db = glbDBObj::dbObj();
		$rs = $db->query($this->sql());
		if(!$this->prec){ 
			$rec = $db->query($this->sql('_rc_recs_'));
			$this->prec = $rec[0]['_rc_recs_'];
		} 
		$this->bar = $this->links();
		return $rs;
	}
	
	function show($kfirst='',$klast='',$type='',$pbase=''){
		$pbase = empty($pbase) ? basReq::getUri(-1,'','page|prec|ptype|pkey') : $pbase; 
		$pcnt = ceil($this->prec/$this->psize);
		$a = $this->bar; $bar = ''; $para = "&prec=$this->prec&page=";
		$p0 = array("{pfirst}","{pprev}","{pnext}","{plast}",);
		$p1 = array("{$para}1","$para".($this->page-1),"$para".($this->page+1),"$para".$pcnt,);
		if($this->opkey){
			$p1[0] .= "&ptype=start";
			$p1[1] .= "&ptype=prev&pkey=$kfirst";
			$p1[2] .= "&ptype=next&pkey=$klast";
			$p1[3] .= "&ptype=end";
		}
		foreach($a as $k=>$v){
			$v = str_replace($p0,$p1,$v);
			$v = str_replace("<li>","\n<li class='pg_$k'>",$v);
			$v = str_replace("{url}",$pbase,$v);
			$bar .= $v; $a[$k] = $v; 
		}
		return $bar;
	}
	
	private function links(){
		$pcnt = ceil($this->prec/$this->psize);
		$a = array(); //$bar = ''; 
		$sFirst = '&laquo;||'.basLang::show('page_First');   $sPrev = '&lt;|'.basLang::show('page_Prev');   
		$sNext = basLang::show('page_Next').'|&gt;';   $sLast = basLang::show('page_Last').'||&raquo;';
		$a['total'] = "<li>$this->prec</li>";
		$a['pagno'] = "<li>$this->page/$pcnt</li>";
		if($pcnt<=1){
			$a['first'] = "<li>$sFirst</li>";
			$a['prev']  = "<li>$sPrev</li>";
			$a['now']   = "<li>$this->page</li>";
			$a['next']  = "<li>$sNext</li>";
			$a['last']  = "<li>$sLast</li>";
		}elseif($this->page==$pcnt){
			$a['first'] = "<li><a href='{url}{pfirst}'>$sFirst</a></li>";
			$a['prev']  = "<li><a href='{url}{pprev}' >$sPrev</a></li>";
			$a['now']   = "<li>$this->page</li>";
			$a['next']  = "<li>$sNext</li>";
			$a['last']  = "<li>$sLast</li>";
		}elseif($this->page==1){
			$a['first'] = "<li>$sFirst</li>";
			$a['prev']  = "<li>$sPrev</li>";
			$a['now']   = "<li>$this->page</li>";
			$a['next']  = "<li><a href='{url}{pnext}' >$sNext</a></li>";
			$a['last']  = "<li><a href='{url}{plast}' >$sLast</a></li>";
		}else{
			$a['first'] = "<li><a href='{url}{pfirst}'>$sFirst</a></li>";
			$a['prev']  = "<li><a href='{url}{pprev}' >$sPrev</a></li>";
			$a['now']   = "<li>$this->page</li>";
			$a['next']  = "<li><a href='{url}{pnext}' >$sNext</a></li>";
			$a['last']  = "<li><a href='{url}{plast}' >$sLast</a></li>";
		}
		$a['goto'] = "<li><input type='text' id='goto' value='$this->page' maxlength='9' onchange=\"alert(1);\"/></li>";
		return $a;
	}
}

