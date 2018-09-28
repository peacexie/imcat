<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
// 素材管理接口
// 随微信规则更新 7000009:请求语义服务失败 

class wmpMaterial extends wmpBasic{
    
    private $tupUrl = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=%s&type=%s';
    private $tgetUrl = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token=%s&media_id=%s';
    
    private $mnewsUrl = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=%s';
    private $mnimgUrl = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=%s';
    private $maddUrl = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=%s';
    
    private $mlistUrl = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=%s'; //这个接口每天调用上限只有10此左右
    private $mdelUrl = 'https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=%s';
    
    function __construct($cfg=array()){ 
        parent::__construct($cfg); 
    }
   // 新增临时素材
    function tmpUpload($path,$type='image'){
        $url = sprintf($this->tupUrl,$this->actoken,$type);
        $paras['media'] = '@'.wysMaterial::getLocal($path); 
        $data = comHttp::doPost($url, $paras, 5); 
        return wysBasic::jsonDecode($data,$this->tupUrl);
    }
    // 获取临时素材
    function tmpGet($media_id){
        $url = sprintf($this->tgetUrl,$this->actoken,$media_id);
        return $url;
    }
    
    // 新增其他类型永久素材
    function matAdd($path){
        $url = sprintf($this->maddUrl,$this->actoken);
        $paras['media'] = '@'.wysMaterial::getLocal($path);
        $data = comHttp::doPost($url, $paras, 3); 
        return wysBasic::jsonDecode($data,$this->maddUrl);
    }
    
    // 新增永久图文素材
    // articles:包含:$title,$medid,$content,$url
    // 
    function matNews($articles,$author='system',$digest=0,$cpic=0){
        $url = sprintf($this->mnewsUrl,$this->actoken);
        $paras['articles'] = array();
        foreach($articles as $k=>$v){
            $paras['articles'][] = array(
                "title" => $v['title'],
                "thumb_media_id" => $v['medid'],
                "author" => $author,
                "digest" => $k==$digest ? 1 : 0,
                "show_cover_pic" => $k==$cpic ? 1 : 0,
                "content" => $v['content'],
                "content_source_url" => $v['url'],
            );    
        }
        $paras = wysBasic::jsonEncode($paras);
        $data = comHttp::doPost($url, $paras, 3); 
        return wysBasic::jsonDecode($data,$this->mnewsUrl);
    }
    
    // 获取素材列表
    // $type = 图片（image）、视频（video）、语音 （voice）、图文（news）
    function mgetList($type,$offset=0,$count=20){
        $url = sprintf($this->mlistUrl,$this->actoken);
        $paras = "{\"type\":\"$type\",\"offset\":$offset,\"count\":$count}";
        //$paras['media'] = '@'.wysMaterial::getLocal($path);
        $data = comHttp::doPost($url, $paras, 3);
        return wysBasic::jsonDecode($data,$this->mlistUrl);
    }
    
    // 删除永久素材
    function mdelMedia($media_id){
        $url = sprintf($this->mdelUrl,$this->actoken);
        $paras = "{\"media_id\":\"$media_id\"}";
        $data = comHttp::doPost($url, $paras, 3);
        return wysBasic::jsonDecode($data,$this->mdelUrl);
    }
    
    /** 获取媒体（具体保存媒体由相关代码实现）
     */
    function loadMedia($mediaid){
        if(strpos($mediaid,'://')){ //文件url格式
            $url = $mediaid;    
            //最近更新，永久图片素材新增后，将带有URL返回给开发者，开发者可以在腾讯系域名内使用（腾讯系域名外使用，图片将被屏蔽）。
        }else{
            $url = sprintf($this->tgetUrl, $this->actoken, $mediaid);
        } 
        $media = comHttp::doGet($url, 3); 
        return $media; //失败为NULL
    }

}
