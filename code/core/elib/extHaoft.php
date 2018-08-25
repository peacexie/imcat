<?php
(!defined('RUN_INIT')) && die('No Init');

class extHaoft{

    public $skcfg = array();
    public $db = null;

    public $city = '';
    public $run = ''; // 运行模式: cmd-命令行, browser-浏览器

    public $types = array(
         // 表名 => 接口key
        'sale' => 'Sale',
        'rent' => 'Lease',
        'dept' => 'Dept',
        'user' => 'User',
        'comp' => 'Comp',
    );
    public $type = '';

    public $files = array();

    function __construct($city){
        // 检查城市
        $cfgs = read('haoft','ex'); 
        if(empty($city) || !isset($cfgs[$city])){
            die('`city($city)` Error!');
        }else{
            $this->city = $city;
            $this->skcfg = $cfgs[$city];
            $this->db = db($cfgs['db-key']);
        }
        // 运行模式
        $this->run = req('run');
        $this->run || $this->run = 'cmd'; // 命令行运行
        $this->files = $cfgs['db-files'];
        comHttp::setCache(5);
    }

    public function syncData($type){
        // 检查类型
        if(!$type || !isset($this->types[$type])){
            die('`type($this->type)` Error!');
        }
        $this->type = $type; 
        // getData
        $last = $this->apiLast(); // 获取last,兼容browser,cmd
        $url = $this->apiUrl($last); //die("$last : $url"); 
        $data = comHttp::doGet($url); //dump($data);
        // deelData
        $earr = json_decode($data,1); 
        $res = $earr; unset($res['DATA']);
        $res['nadd'] = $res['nupd'] = $res['nskip'] = $res['nok'] = $res['nbad'] = 0;
        if(!empty($earr['DATA'])){
            foreach($earr['DATA'] as $row){
                $row['city'] = $this->city;
                $this->updRow($row,$res);
            }
        }
        // debug-start
        return $res;
    }
    public function updRow($row,&$res){
        $table = $this->type;
        $kid = strtoupper(strtolower($this->types[$this->type]))."_ID";
        $kval = $row[$kid];
        $old = $this->db->table($table)->where("$kid='$kval'")->find();
        $rdb = null; 
        $isrs = in_array($this->type,array('sale','rent')); // 租售
        if(empty($old)){
            $act = 'nadd'; 
            $data = $this->rowFields($row, $table);
            $rdb = $this->db->table($table)->data(in($data))->insert(0);
            if($isrs){
                $data = $this->rowFields($row, $table.'info');
                $this->db->table($table.'info')->data(in($data))->insert(0);
            } 
        }elseif($isrs && $old['UPDATE_TIME'].'.999'<$row['UPDATE_TIME']){
            // db:2017-04-22 09:23:52 , api:2017-04-22 09:23:52.123(有小数点) , 1秒内的误差,容忍吧！
            $act = 'nupd';
            unset($row[$kid]);
            $data = $this->rowFields($row, $table);
            $rdb = $this->db->table($table)->data(in($data))->update(0);
            if(in_array($this->type,array('sale','rent'))){
                $data = $this->rowFields($row, $table.'info');
                $this->db->table($table.'info')->where("$kid='$kval'")->data(in($data))->update(0);
            }
        }else{
            $act = 'nskip'; 
        }
        $res[$act]++;
        $key = $rdb ? 'nok' : 'nbad';
        $res[$key]++;
        // debug-start
    }

    // 单个表一行数据
    public function rowFields($row, $tab){
        $fields = $this->files[$tab];
        $res = array();
        foreach ($row as $key=>$val) {
            if(strpos($fields,$key)){
                $res[$key] = $val;
            }
        }
        if(in_array($tab,array('sale','rent')) && $res['BUILD_ID']<0){
            $res['BUILD_ID'] = 0;
        }
        return $res;
    }

    // 
    public function apiLast($last=''){
        //$logfp = APP_PATH.'Conf/chaoft.log';
        $logkey = "{$this->city}_{$this->type}";
        if($last=='clear'){ // clear-1
            $this->db->table('cache')->where("kid='$logkey'")->delete();
        }elseif(!empty($last)){ // save
            $data = array('kid'=>$logkey,'val'=>$last);
            $this->db->table('cache')->data()->replace();
        }elseif($this->run=='cmd'){ // read
            $row = $this->db->table('cache')->where("kid='$logkey'")->find();
            $last = $row ? $row['val'] : '2015-01-01'; 
            return $last;
        }else{ // get.def: browser下使用
            $last = res('last'); // null=daily/2015-01-01/2017-05-18%2019:43:53.953
            if(empty($last)||$last=='daily'){
                $time = time()-$this->daily*3600;
                $last = date('Y-m-d H:i:s',$time);
            }
            return $last;
        }
    }
    
    // type: Sale, Lease, Comp, User, Dept
    public function apiUrl($last=''){
        $apiType = $this->types[$this->type];
        $key = "LAST_TIME_".strtoupper($apiType)."";
        $last && $last = "$key=".str_replace(' ','%20',$last);
        $url = "http://user.haofang.net/hftWebService/web/openApi/data/get{$apiType}List?";
        $param = "COMP_NO={$this->skcfg['ak']}&SYNC_VERIFYID={$this->skcfg['as']}&$last";
        if($apiType=='Comp'){ $url = str_replace('List?','Info?',$url); }
        return $url.$param;
    }

    // https://linzhi.haofang.net/sale/
    public function syncArea(){
        $city = $this->city;
        $db = $this->db;
        $url = "https://$city.haofang.net/sale/";
        $data = comHttp::doGet($url);
        $data = basElm::getPos($data,'<dt>区域:</dt>(*)</dl>');
        preg_match_all("/\/sale\/a(\w+)\/\"\s*>(\S+)<\/a>/is",$data,$arr);
        // <a href="https://linzhi.haofang.net/sale/a2466/" >林芝县</a>
        $res = array();
        if(!empty($arr[2])){
            foreach($arr[2] as $k1=>$v1){
                $pid = $arr[1][$k1];
                echo "<br><b>{$pid}:$v1</b><br>\n";
                // db-pid=0
                $row = array('code'=>$pid,'title'=>$v1,'city'=>$city,'pid'=>0);
                $old = $db->table('area')->where("city='$city' AND pid='0' AND title='$v1'")->find();
                if(empty($old)){
                    $rf = $db->table('area')->data($row)->insert(0);
                } // db-pid=(end) */
                $data = comHttp::doGet($url."/a$pid"); 
                $data = basElm::getPos($data,array('class="business-list"','</ul>'));
                preg_match_all("/\/sale\/a(\w+)\-b(\w+)\/\"\s*id=\"b\w+\">(\S+)<\/a>/is",$data,$ar2); 
                // <a href="https://linzhi.haofang.net/sale/a2467-b22094/"id="b22094">
                if(!empty($ar2[3])){
                    foreach($ar2[3] as $k2=>$v2){
                        $kid = $ar2[2][$k2];
                        echo " {$kid}:$v2 , \n";
                        // db-sub=0
                        $row = array('code'=>$kid,'title'=>$v2,'city'=>$city,'pid'=>$pid);
                        $old = $db->table('area')->where("city='$city' AND pid='$pid' AND title='$v2'")->find();
                        if(empty($old)){
                            $rf = $db->table('area')->data($row)->insert(0);
                        } // db-sub=(end) */
                    }
                }
            }
        }
        //dump($ar2); dump($arr);
    }

}
