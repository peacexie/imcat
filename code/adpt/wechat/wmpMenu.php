<?php
(!defined('RUN_INIT')) && die('No Init');
// 菜单管理
// 随微信规则更新

class wmpMenu extends wmpBasic{

    protected $menuUrl = 'https://api.weixin.qq.com/cgi-bin/menu/%s?access_token=%s';
    protected $oauth = NULL;
    
    function __construct($cfg=array()){
        parent::__construct($cfg); 
    }
    
    // 查询当前使用的自定义菜单结构
    function menuGet(){
        $url = sprintf($this->menuUrl, 'get', $this->actoken);
        $data = comHttp::doGet($url,3);
        return wysBasic::jsonDecode($data,$this->menuUrl);
    }
    
    // 创建菜单
    function menuCreate($mcfg=array()){ 
        $ucfg['button'] = $mcfg;
        $url = sprintf($this->menuUrl, 'create', $this->actoken); 
        $paras = wysBasic::jsonEncode($ucfg); 
        $data = comHttp::doPost($url, $paras, 3);
        return wysBasic::jsonDecode($data,$this->menuUrl);
    }    
    
    // 删除当前使用的自定义菜单
    function menuDelete(){
        $url = sprintf($this->menuUrl, 'delete', $this->actoken);
        $data = comHttp::doGet($url,3);
        return wysBasic::jsonDecode($data,$this->menuUrl);
    }
    
}
