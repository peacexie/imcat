<?php
namespace imcat;

class extHouse{

    public $db = null;

    public $city = '';

    public $types = array('sale', 'lease', 'dept', 'user', 'comp');
    public $type = '';

    public $fields = array();

    function __construct($city=''){
        // 检查城市
        $cfgs = read('haoft','ex'); 
        if(empty($city) || !isset($cfgs[$city])){
            die('`city($city)` Error!');
        }else{
            $this->city = $city;
            $this->skcfg = $cfgs[$city];
            $this->db = db($cfgs['db-key']);
        }
        // 检查城市
        $cfgs = read('haoft','ex'); 
    }

    // --------------------------------------------

    // 区域/商圈 self::areas()
    static function areas($city='', $pid='')
    {
        //$cfgs = read($mod, 'ex');
        //return empty($cfgs[$key]) ? array() : $cfgs[$key];
    }

    // 字段配置 self::exfcfg(house,wy)
    static function exfcfg($mod='house', $key='')
    {
        $cfgs = read($mod, 'ex');
        return empty($cfgs[$key]) ? array() : $cfgs[$key];
    }

}
