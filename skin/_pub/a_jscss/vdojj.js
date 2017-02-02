
function vdoPlay(pid, vurl, img, title){
    var vdoObj = new Iva(pid,{ //父容器id
       appkey: 'rkUAC0ZHl',//必填，请在控制台查看应用标识
       video: vurl,//必填，播放地址
       cover: img,//选填，视频封面url
       title: title,//选填，建议填写方便后台数据统计
       videoStartPrefixSeconds: 0,//选填，跳过片头，默认为0
       videoEndPrefixSeconds: 0,//选填，跳过片尾，默认为0
       //autoplay: true,//选填，是否自动播放，默认为false
       editorEnable: false, // 选填，当用户登录之后，是否允许加载编辑器，默认为true
       vorEnable: false, // 选填，是否允许加载灵悟，默认为true
       vorStartGuideEnable: false //选填， 是否启用灵悟新人引导，默认为true
    });
}

/*
   appkey: 'rkUAC0ZHl',//必填，请在控制台查看应用标识
   video: 'http://img.fzg360.com/dg/video/DCCS/2014/09/912wangzhou80pin.flv',//必填，播放地址（例如：http://v.youku.com/v_show/id_XMTY5NDg2MzY5Ng==.html）
   title: '小视频',//选填，建议填写方便后台数据统计
   cover: 'http://img.fzg360.com/sz/userfiles/image/20160920/201342301b5f5c59dc5020_230_150.jpg',//选填，视频封面url
   playerUrl: '', //选填，第三方播放器与Video++互动层的桥接文件，由Video++官方定制提供，默认为空
   videoStartPrefixSeconds: 0,//选填，跳过片头，默认为0
   videoEndPrefixSeconds: 0,//选填，跳过片尾，默认为0
   /-* 以下参数可以在“控制台->项目看板->应用管理->播放器设置” >进行全局设置，前端设置可以覆盖全局设置 *-/
   skinSelect: 0,//选填，播放器皮肤，可选0、1、2，默认为0，
   autoplay: true,//选填，是否自动播放，默认为false
   rightHand: true,//选填，是否开启右键菜单，默认为false
   autoFormat: false,//选填，是否自动选择最高清晰度，默认为false
   bubble: true,//选填，是否开启云泡功能，默认为true
   jumpStep: 10,//选填，左右方向键快退快进的时间
   tagTrack: false,//选填，云链是否跟踪，默认为false
   tagShow: false,//选填，云链是否显示，默认为false
   tagDuration: 5,//选填，云链显示时间，默认为5秒
   tagFontSize: 16,//选填，云链文字大小，默认为16像素
   editorEnable: true, // 选填，当用户登录之后，是否允许加载编辑器，默认为true
   vorEnable: true, // 选填，是否允许加载灵悟，默认为true
   vorStartGuideEnable: true //选填， 是否启用灵悟新人引导，默认为true
*/
