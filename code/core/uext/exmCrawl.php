<?php

// 
class exmCrawl{

	//static $blnka = 'href = "';

	public $flags = array(
		'p1' => 'id="content_left"',
		'p2' => 'id="page"',
		'p3' => 'id="content_bottom"',
	);

    //构造方法
    function __construct($site,$kw='',$pn=73){
    	$kw = $kw ? urlencode($kw)."%20" : '';
    	$url = "https://www.baidu.com/s?wd={$kw}site%3A$site&pn={$pn}0";
    	$this->initPage($url);
    }

    function initPage($url){
        $html = comHttp::doGet($url); //dump($html);
        $flags = $this->flags;
        $links = basElm::getPos($html,$flags['p1'].'(*)'.$flags['p2']);
        $pages = basElm::getPos($html,$flags['p2'].'(*)'.$flags['p3']);
        $tmp = explode('href = "',$links);
        $links = array();
        foreach ($tmp as $val) {
        	$val = substr($val,0,strpos($val,'"'));
        	if(!strpos($val,'link?url=')) continue;
        	$links[] = $val;
        	//dump($val);
        }
        $pages = basElm::getArr($pages,'<span class="pc">(*)</span>');
        $this->data = array('links'=>$links,'pages'=>$pages,'url'=>$url);
        return;
    }



}
