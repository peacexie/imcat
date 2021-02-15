<?php
namespace imcat\vapi;

use imcat\basEnv;
use imcat\basOut;
use imcat\comSession;
use imcat\comCookie;
use imcat\comConvert;
use imcat\comTypes;
use imcat\comHttp;
use imcat\extCargo;
use imcat\extGbuy;
use imcat\glbDBExt;
use imcat\vopApi;

/*
    部分接口，无实际数据调试；根据情况完善
*/

# 阳光公采API

class gbuyCtrl extends vopApi{

    public $ucfg = array();
    public $vars = array();
    protected $post;
    protected $parr;

    //function __destory(){  }
    function __construct($ucfg=array(), $vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->init();  
    }
    // 
    function init(){
        die('2021-0209');
        //dump($this->ucfg);
        $this->post = file_get_contents("php://input");
        $this->parr = json_decode($this->post, 1);
        #dump($_POST); dump($this->post); dump($this->parr);
        if(in_array($this->ucfg['key'],['actoken','testp'])){
            //
        }else{
            #extGbuy::checkActoken($this->post);
        }
    }

    // 获取接口令牌
    function access_tokenAct(){
        extGbuy::getActoken($this->parr);
    }
    // 获取商品池
    function get_poolsAct($retab=0){
        $pool = db()->table('exd_umod')->where("cfgs LIKE'%govbuy%'")->select();
        $list = [];
        foreach($pool as $pk => $pv) {
            $list[] = ['name'=>$pv['title'],'id'=>intval($pv['kid'])];
        }
        if($retab){ return $list; }
        $res['success'] = true;
        $res['result'] = $list; // dump($res);
        return $this->view($res);
    }
    // 获取商品池内SKUS
    function skusAct(){
        $tab = $this->get_poolsAct(1);
        $ids = array_column($tab, 'id');
        if(isset($this->parr['catalog_id'])){
            $where = "attmod='".$this->parr['catalog_id']."'";
        }else{
            $where = "attmod IN('".implode("','",$ids)."')";
        }
        $data = db()->table('docs_cargo')->field('did')->where($where)->select(); 
        $list = [];
        foreach($data as $rk => $rv) {
            $list[] = $rv['did'];
        }
        $res['success'] = true;
        $res['result'] = $list; // dump($res);
        return $this->view($res);
    }

    // 获取商品详情
    function viewAct(){
        $this->parr['sku'] = '2020-a3-gey6'; // for Test
        $sku = isset($this->parr['sku']) ? $this->parr['sku'] : '';
        if(empty($sku)){
            $res['success'] = false;
            $res['desc'] = 'no sku';
            return $this->view($res);
        }
        $row = data('cargo.join', "did='$sku'", 1);
        if(!$row){
            $res['success'] = false;
            $res['desc'] = 'no goods';
            return $this->view($res);
        }
        // 
        $res = []; //dump($row);
        include dirname(__DIR__)."/gbuy/type_comm.php";
        return $this->view($res);
    }
    // 价格接口
    function pricesAct(){
        $this->parr['skus'] = '2020-a3-gey6,2020-a9-dtx8'; // for Test
        $skus = isset($this->parr['skus']) ? $this->parr['skus'] : '';
        $skua = explode(',', $skus);
        if(empty($skua)){
            $res['success'] = false;
            $res['desc'] = 'no sku';
            return $this->view($res);
        }
        $list = [];
        foreach($skua as $sk=>$sku) {
            $row = data('cargo', "did='$sku'", 1);
            $itm = ['sku'=>$sku, 'price'=>$row['price'], 'mall_price'=>$row['price']];
            $list[] = $itm;
        }
        $res['success'] = true;
        $res['result'] = $list; // dump($res);
        return $this->view($res);
    }
    // 图片接口
    function imagesAct(){
        $this->parr['skus'] = '2020-a3-gey6,2020-a9-dtx8'; // for Test
        $skus = isset($this->parr['skus']) ? $this->parr['skus'] : '';
        $skua = explode(',', $skus);
        if(empty($skua)){
            $res['success'] = false;
            $res['desc'] = 'no sku';
            return $this->view($res);
        }
        $list = [];
        foreach($skua as $sk=>$sku) {
            $row = data('cargo.join', "did='$sku'", 1);
            $imgs = extCargo::pics($row['rel_pic'], 0);
            $itab = [];
            foreach($imgs as $ik=>$iv) {
                $itab[] = ['path'=>$iv, 'order'=>999-$ik];
            }
            $itm = ['sku'=>$sku, 'images'=>$itab];
            $list[] = $itm;
        }
        $res['success'] = true;
        $res['result'] = $list; // dump($res);
        return $this->view($res);
    }

    // 获取一级地址
    function provincesAct(){
        $arr = read('china.i'); 
        $data = comTypes::getSubs($arr, '0', '1');
        $list = [];
        foreach($data as $ik=>$row) {
            $list[$row['title']] = $ik;
        }
        $res['success'] = true;
        $res['result'] = $list; // dump($res);
        return $this->view($res);
    }
    // 获取二级地址
    function citiesAct(){
        $this->parr['id'] = 'cnhn'; // for Test
        $pid = isset($this->parr['id']) ? $this->parr['id'] : '';
        $arr = read('china.i'); 
        $data = comTypes::getSubs($arr, $pid, '2'); 
        $list = [];
        foreach($data as $ik=>$row) {
            $list[$row['title']] = $ik;
        }
        $res['success'] = true;
        $res['result'] = $list; // dump($res); 
        return $this->view($res);
    }
    // 获取三级地址
    function getCountyAct(){
        $this->parr['id'] = 'c0735'; // for Test
        $pid = isset($this->parr['id']) ? $this->parr['id'] : '';
        $arr = read('china.i'); 
        $data = comTypes::getSubs($arr, $pid, '3'); 
        $list = [];
        foreach($data as $ik=>$row) {
            $list[$row['title']] = $ik;
        }
        $res['success'] = true;
        $res['result'] = $list;
        return $this->view($res);
    }

    // 库存接口
    function stocksAct(){
        $this->parr['skus'] = '2020-a3-gey6,2020-a9-dtx8'; // for Test
        $skus = isset($this->parr['skus']) ? $this->parr['skus'] : '';
        $area = isset($this->parr['area']) ? $this->parr['area'] : '';
        $skua = explode(',', $skus);
        if(empty($skua)){
            $res['success'] = false;
            $res['desc'] = 'no sku';
            return $this->view($res);
        }
        $list = [];
        foreach($skua as $sk=>$sku) {
            $row = data('cargo', "did='$sku'", 1);
            $stock = empty($row['stock']) ? 0 : $row['stock'];
            $des = $stock ? '无货' : '有货';
            $itm = ['sku'=>$sku, 'num'=>$stock, 'area'=>$area, 'desc'=>$des];
            $list[] = $itm;
        }
        $res['success'] = true;
        $res['result'] = $list; // dump($res);
        return $this->view($res);
    }
    // 上下架状态接口
    function shelf_statesAct(){
        $this->parr['skus'] = '2020-a3-gey6,2020-a9-dtx8'; // for Test
        $skus = isset($this->parr['skus']) ? $this->parr['skus'] : '';
        $skua = explode(',', $skus);
        if(empty($skua)){
            $res['success'] = false;
            $res['desc'] = 'no sku';
            return $this->view($res);
        }
        $list = [];
        foreach($skua as $sk=>$sku) {
            $row = data('cargo', "did='$sku'", 1);
            $itm = ['sku'=>$sku, 'state'=>$row['show']];
            $list[] = $itm;
        }
        $res['success'] = true;
        $res['result'] = $list; // dump($res);
        return $this->view($res);
    }

    // 预下订单接口
    function submitAct(){
        // check
        $sku = isset($this->parr['sku']) ? $this->parr['sku'] : [];
        $order_price = isset($this->parr['order_price']) ? $this->parr['order_price'] : 0;
        $freight = isset($this->parr['freight']) ? $this->parr['freight'] : 0;
        //$skua = explode(',', $skus);
        if(empty($sku)){
            $res['success'] = false;
            $res['desc'] = 'no sku';
            return $this->view($res);
        }
        // 组订单
        $kar = glbDBExt::dbAutID('coms_cocar');
        # ...
        // return
        $result['mall_order_id'] = $kar[0];
        $result['sku'] = $sku;
        $result['orderPrice'] = $order_price;
        $result['freight'] = $freight;
        $res['success'] = true;
        $res['result'] = $list; // dump($res);
        return $this->view($res);
    }
    // 查询订单信息接口
    function selectAct(){
        //
    }

    // -
    function order_returnAct(){
        $order_sn = isset($this->parr['order_sn']) ? $this->parr['order_sn'] : '';
        $type = isset($this->parr['type']) ? $this->parr['type'] : '';
        $skus = isset($this->parr['skus']) ? $this->parr['skus'] : '';
        /*$goods['sku']='PC0103019108';
        $goods['num']='1';
        $goods['price']=4099.0;
        array_push($skus,$goods);*/
        $result['order_id'] = $order_sn;
        $result['type'] = $type;
        $result['skus'] = $skus;
        $result['order_service_id'] = 1; // 退换货的服务号id
        // update - order
        # ...
        // 
        $res['success'] = true;
        $res['result'] = $result; // dump($res);
        return $this->view($res);
    }
    // -
    function fittingsAct(){
        //
    }
    // -
    function get_servicesAct(){
        //
    }
    // -
    function get_invoice_listAct(){
        //
    }
    // -
    function get_similar_skuAct(){
        //
    }

    function testpAct(){
        #dump('-test-start-');
        #die('-test-end-');
    }

}


/*


if ($gc_action == 'fittings'){

    $cityId = $data->cityId;

    $fittings = [];
    $fitting['fitting_id'] = 1;
    $fitting['genre'] = 1;
    $fitting['name'] = "上门安装";
    $fitting['price'] = 10;

    array_push($fittings,$fitting);

    $result['result']['fittings'] = $fittings;
    $result['success'] = true;

    die(json_encode($result));
}

if ($gc_action == 'get_services'){

    $sku = isset($data->skus ) ? $data->skus : '';
    $sku = explode(',',$sku);
    if(empty($sku)){
        $result['success'] = false;
        $result['desc'] = 'no sku';
        die(json_encode($result));
    }

    $result['result'] = [];
    foreach($sku as $value){
        $row = null;//getProductServiceInfo($value,$bus_if_id);

        $array['sku_id'] = trim($value);

        //测试数据
        $row['name']='保修服务';
        $row['url']='http://www.zhoulinoffice.com';
        $row['details'] = [];

        $detail['sku'] = $value;
        $detail['name'] = '按厂商保修期限保修';
        $detail['title'] = '按厂商保修期限保修';
        $detail['price'] = 0;
        $detail['position'] = 1;
        array_push($row['details'],$detail);

        $array['services'] = $row;
        array_push($result['result'],$array);
    }

    $result['success'] = true;
    $result['desc'] = '查询成功';

    die(json_encode($result));
}
if ($gc_action == 'get_invoice_list'){

    $order_ids = isset($data->order_ids ) ? $data->order_ids : '';
    $order_ids = explode(',',$order_ids);
    if(empty($order_ids)){
        $result['success'] = false;
        $result['desc'] = 'no order_id';
        die(json_encode($result));
    }

    $result['result'] = [];
    foreach($order_ids as $value){
        $row = "http://www.gdzhuoke.com/fpimages/6275698a35e6fbf1dbbd95110ba1dda.png";
        //getProductServiceInfo($value,$bus_if_id);
        $array[$value] = $row;
        array_push($result['result'],$array);
    }

    $result['success'] = true;
    $result['desc'] = '查询成功';

    die(json_encode($result));
}

if ($gc_action == 'get_similar_sku'){
    $sku = isset($data->sku)? $data->sku:'';

    //$sku = 'HC01100128346';
    $result['result'] = [];

    $res = getSimilar($sku);

    $result['success'] = true;
    $result['desc'] = 'success';
    $result['result'] = $res;
    die(json_encode($result));
}


if ($gc_action == 'select'){

    $order_sn = $data->order_id;

    $sql = "select * from ".$GLOBALS['ecs']->table("order_info")." where order_sn = '$order_sn' and bus_if_id=$bus_if_id; ";
    $order_info = $GLOBALS['db']->getRow($sql);

    $result['order_id'] = $order_info['order_sn'];
    $result['state'] = intval($order_info['order_status']) ;
    $result['total_price'] = floatval($order_info['order_amount']) ;

    $order_id =  $order_info['order_id'];
    $sql = "select * from ".$GLOBALS['ecs']->table("order_goods")." where order_id = '$order_id' ";
    $order_goods = $GLOBALS['db']->getAll($sql);

    //订单内商品
    $skus = array();

    foreach ($order_goods as $key=>$goods){
        $goods_item['sku'] = $goods['goods_sn'];
        $goods_item['num'] = intval($goods['goods_number']);
        $goods_item['price'] =floatval($goods['goods_price']);
        array_push($skus,$goods_item);
    }
    $result['skus'] = $skus;

    //订单内的退货商品
    $return_skus = array();
    $result['return_skus'] = $skus;
    $res['result'] = $result;
    $res['success'] = true;
    die(json_encode($res));
}

// attmod [1-笔记本属性, 2-台式机属性, 58-空调机属性]单独处理 - Peace/2020-08-31
$fp = "/type_{$row['attmod']}.php"; // tester __DIR__.
if(file_exists(__DIR__.$fp)){ // 特殊处理产品
    include __DIR__.$fp;
}elseif(in_array($row['attmod'],['1','2','58'])){ //标准参数产品
    include __DIR__."/type_standard.php";
}else{ // 之前默认处理
    include __DIR__."/type_comm.php";
}

*/
