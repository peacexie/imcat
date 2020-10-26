<?php
(!defined('RUN_INIT')) && die('No Init');

use imcat\comStore;
use imcat\extGbuy;
use imcat\extCargo;

$attrs = extCargo::fieldAtts($row); //dump($attrs);
#$yyy = xxx($row[''], 1);

$result['sku'] = $row['did'];
$result['url'] = surl('comm:cargo.'.$row['did'], '', 1);
$result['model'] = $row['xinghao']; // 非必须,型号
$result['weight'] =$row['weight']; //非必须 重量
$result['image_path'] = extCargo::pics($row['rel_pic']); 
$result['state'] = 1; //intval($row['bus_on_sale']); //非必须【1：上架0：下架】
$result['brand_name'] = extCargo::keyType('brand', $row['brand']); // 商品品牌
$result['name'] = $row['title'];
$result['product_area'] = extCargo::oneAttr($attrs, '产地', 'china'); // 非必须,产地
$result['upc'] = extCargo::oneAttr($attrs, '条码,条形码', '无'); // 非必须,条形码
$result['unit'] = extCargo::oneAttr($attrs, '单位,销售单位', '台'); // 非必须,销售单位
$result['category'] = extCargo::keyUmod($row['attmod']); // 采购品目 //非必须,类别
$result['service'] = $row['serv']; //
$result['code_69'] = '';
#$rbk = $row;

// 属性
$result['attributes'] = extCargo::umodAtts($row, 1); //$attributes;
// 描述 file_get_contents(ROOT_PATH."data/down-param/$fp.htm")
$result['introduction'] = str_replace(["\r","\n"], ['',''], $row['detail']); 
// 参数，多一个少一个都不行？？？【包含包装清单、主体参数、基本参数等】
$result['param'] = extCargo::attsParam($row);
// 配件 主机 x1 , 操作系统 x1, 显示器 x1, 鼠标 x1, 键盘 x1
$result['ware'] = extCargo::relParts($row, 'ware');

$result['sale_actives'] = 0;
$result['price_voucher_type'] = 0;
$result['qualities'] = '';
$result['is_apply'] = false;
$result['code_69_type'] = 1;
//qualities：质量标准（多个质量标准，用英文","分隔）【product_extend_attributes 参数】
//is_apply: 是否采购人申请的商品（true 是，false 否）【product_extend_attributes 参数】

$res = [];
$res['success'] = true;
$res['result'] = $result;

//dump($res);

