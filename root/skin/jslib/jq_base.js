
// Cookie操作
// easy_validator.js

// *** Cookie操作 =========================================================================================
// $.cookie('the_cookie'); // 获得cookie
// $.cookie('the_cookie', 'the_value'); // 设置cookie
// $.cookie('the_cookie', 'the_value', { exps: 7 }); //设置带时间的cookie，单位：秒
// $.cookie('the_cookie', '', { exps: -1 }); // 删除
// $.cookie('the_cookie', null); // 删除 cookie
// $.cookie('the_cookie', 'the_value', {exps: 7, path: '/', domain: 'jquery.com', secure: true});//新建一个cookie 包括有效期 路径 域名等

jQuery.cookie = function(name, value, options) {
	if (typeof value != 'undefined') {
		options = options || {};
		if (value === null) {
			value = '';
			options.exps = -1;
		}
		var exps = '';
		if (options.exps && (typeof options.exps == 'number' || options.exps.toUTCString)) {
			var date;
			if (typeof options.exps == 'number') {
				date = new Date();
				date.setTime(date.getTime() + (options.exps * 1000));
			} else {
				date = options.exps;
			}
			exps = '; exps=' + date.toUTCString();
		}
		var path = options.path ? '; path=' + options.path: '';
		var domain = options.domain ? '; domain=' + options.domain: '';
		var secure = options.secure ? '; secure': '';
		document.cookie = [name, '=', encodeURIComponent(value), exps, path, domain, secure].join('');
	} else {
		var cookieValue = null;
		if (document.cookie && document.cookie != '') {
			var cookies = document.cookie.split(';');
			for (var i = 0; i < cookies.length; i++) {
				var cookie = jQuery.trim(cookies[i]);
				if (cookie.substring(0, name.length + 1) == (name + '=')) {
					cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
					break;
				}
			}
		}
		return cookieValue;
	}
};

//* easy_validator.js =====================================================================================
	//Copyright (C) 2009 - 2012
	//WebSite:	Http://wangking717.javaeye.com/
	//Author:		wangking
	// 1. vtip -=> evf_vtip
	// 2. validate -=> evf_validate
//*/

$(function(){
	var xOffset = -20; // x distance from mouse
    var yOffset = 20; // y distance from mouse  
	//input action
	$("[reg],[url]:not([reg]),[tip]").hover(
		function(e) {
			if($(this).attr('tip') != undefined){
				var top = (e.pageY + yOffset);
				var left = (e.pageX + xOffset);
				if (typeof(_sys_path)=='undefined'){
				   _sys_path = '/';
				}
				$('body').append( '<p id="evf_vtip"><img id="evf_vtipArrow" src="'+_cbase.run.roots+'/skin/a_img/easy_vicon.png"/>' + $(this).attr('tip') + '</p>' );
				$('p#evf_vtip').css("top", top+"px").css("left", left+"px");
				try{$('p#evf_vtip').bgiframe();}catch(ex){}
			}
		},
		function() {
			if($(this).attr('tip') != undefined){
				$("p#evf_vtip").remove();
			}
		}
	).mousemove(
		function(e) {
			if($(this).attr('tip') != undefined){
				var top = (e.pageY + yOffset);
				var left = (e.pageX + xOffset);
				$("p#evf_vtip").css("top", top+"px").css("left", left+"px");
			}
		}
	).blur(function(){
		if($(this).attr("url") != undefined){
			evf_check_ajax($(this));
		}else if($(this).attr("reg") != undefined){
			evf_validate($(this));
		}
	});
	
	$("form").submit(function(){
		var isSubmit = true;
		var msgarr = new Array();
		var userCheck = $(this).attr("usercheck");
		$(this).find("[reg],[url]:not([reg])").each(function(){
			if($(this).attr("reg") == undefined){
				if(!evf_check_ajax($(this))){
					isSubmit = false;
					msgarr.push(evf_errMessage(this));
				}
			}else{
				if(!evf_validate($(this))){
					isSubmit = false;
					msgarr.push(evf_errMessage(this));
				}
			}
		});
		if(userCheck){ 
			eval("isSubmit = evf_"+userCheck+"(msgarr,isSubmit);");
			return isSubmit;
		}else if(typeof(evf_isExtends) != "undefined"){
   			return evf_extendsValidate(msgarr,isSubmit);
   		}else{
			evf_errAlert(msgarr,isSubmit);
			return isSubmit;
		}
	});
	
});
function evf_errAlert(msgarr,isSubmit){
	if(!isSubmit){
		var msgstr = '';
		for(var i=0;i<msgarr.length;i++){
			msgstr += '\n'+i+'. '+msgarr[i];
		}
		alert('请检查表单的规范性与完整性：\n'+msgstr);	
	}
}
function evf_errMessage(e){
	var imsg = $(e).attr("tip");
	imsg = imsg ? imsg : $(e).attr("name")+'不规范';
	if(imsg.indexOf('</body>')>0 || imsg.indexOf('</BODY>')>0){
		imsg = jsText(imsg);
		imsg = imsg.replace(/\s+/g,"");
		//if(imsg.length>120)	 imsg = imsg.substring(0,100);
	}
	return imsg;
}

// key:3-12,tit:2-12,str:10+
// n(+0-)(id)
// fix:tel,email,url,file,image,
function evf_getRegs(obj){
	var str = obj.attr("reg");
	var re = str, flag = '';
	if(str.substring(0,4)=='nul:'){
		flag = str.substring(0,4);	
		re = str = str.substring(4);
	}
	if(str=='fix:tel'){ // 0769-12345678; 0769-12345678#0123; 0086-769-12345678#0123
        re = '^([0-9]{1})([0-9\-]{3,12})?[0-9]{1}(#[0-9]{2,5})?$';
	}else if(str=='fix:email'){ // peace_xie@qq.com
		re = '^\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*$';
	}else if(str=='fix:uri'){ // peace_xie@qq.com
		re = '^(http|https|ftp)\\://\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*(\\S)*$';
		// /^\s*https?:\/\/(?:[\w\-]+\.)+[a-z]{2,4}(?:\:\d{1,5})?(?:\/.*)?\s*$/ 
	}else if(str=='fix:file'){ // http://www.txmao.com/?wpwe
		var image = 'gif|jpg|jpeg|png|swf|flv|';
		var media = 'swf|flv|mp3|wav|wma|wmv|mid|avi|mpg|asf|rm|rmvb|';
		var files = 'doc|docx|xls|xlsx|ppt|htm|html|txt|zip|rar|gz|bz2|';
		re = '.+\\.('+image+media+files+'Peace!DB)$';
	}else if(str=='fix:image'){ // 
		re = '.+\\.(gif|jpg|jpeg|png)$';	
	}else if(str=='fix:xid'){ //  xie_ys@08-cms.com  @-_.
		re = '^[0-9]{4}[a-zA-Z0-9@\\-]{6,15}$';
	}else if(str.indexOf('vimg:')==0){ // 认证码
		var a = str.substring(5).split('-'); 
		re = '^[a-zA-Z0-9]{'+a[0]+','+a[1]+'}$';
	}else if(str.indexOf('var:')==0){ //  xie_123
		var a = str.substring(4).split('-'); 
		var a0 = a[0]-1, a1 = a[1]-1;
		re = '^[a-zA-Z][a-zA-Z0-9_]{'+a0+','+a1+'}$';
	}else if(str.indexOf('key:')==0){ //  xie_ys@08-cms.com  @-_.
		var a = str.substring(4).split('-'); 
		var a0 = a[0]-1, a1 = a[1]-1;
		re = '^[a-zA-Z][a-zA-Z0-9@_\\-\\.]{'+a0+','+a1+'}$';
	}else if(str.indexOf('tit:')==0){ // @/_|-.汉字
		var a = str.substring(4).split('-'); 
		re = '^[^\\<\\>\\"\\\'\\\\\\\r\\\n]{'+a[0]+','+a[1]+'}$'; // \\/\\*\\|\\?
		// ^[a-zA-Z@0-9/_\\|\\-\\.\\u4e00-\\u9fa5\\.\\:\\(\\)\\+\\=\\ ]{'+a[0]+','+a[1]+'}$
	}else if(str.indexOf('str:')==0){ //^.{5,100}$
		str = str.replace('+','-123000123');
		var a = (str+"-").substring(4).split('-'); 
		if(a[1].length==0) a[1] = 123000123 //123M,够大了
		re = '^[^\\b]{'+a[0]+','+a[1]+'}$'; 
	//}else if("(n+i:,n-i:,n+d:,n-d:)".indexOf((str+"))))").substr(0,4))>00){ // 
	}else if(str.indexOf('n+i')==0){ // 2,2323
		re = '^[0-9]{1,15}$';
	}else if(str.indexOf('n-i')==0){ // 2,-2323
		re = '^([\\-]{0,1})?[0-9]{1,15}$';
	}else if(str.indexOf('n+d')==0){ // 2,-23.23
		re = '^[0-9]{1,15}([\\.][0-9]{1,15})?$';
	}else if(str.indexOf('n-d')==0){ // 2,-23.23
		re = '^([\\-]{0,1})?[0-9]{1,15}([\\.][0-9]{1,15})?$';
	}
	return reTab = {re:re,flag:flag}; 
}

function evf_validate(obj){
	var objValue = obj.prop("value");
	var reTab = evf_getRegs(obj);
	var reg = new RegExp(reTab.re);
	if(objValue.length==0&&reTab.flag=='nul:') return true;
	if(!reg.test(objValue)){
		evf_change_error(obj,"add");
		evf_change_tip(obj,null,"remove");
		return false;
	}else{
		if(obj.attr("url") == undefined){
			evf_change_error(obj,"remove");
			evf_change_tip(obj,null,"remove");
			return true;
		}else{
			return evf_check_ajax(obj);
		}
	}
}

function evf_check_ajax(obj){
	var url_str = obj.attr("url");
	if(url_str.indexOf("?") != -1){ url_str = url_str+"&"; }
	else                          { url_str = url_str+"?"; }
	url_str += obj.prop("name")+"="+obj.prop("value")+"&"+_cbase.safil.url;
	var feed_back = $.ajax({url: url_str,cache: false,async: false}).responseText;
	feed_back = feed_back.replace(/(^\s*)|(\s*$)/g, "");
	if(feed_back == 'success'){
		evf_change_error(obj,"remove");
		evf_change_tip(obj,null,"remove");
		return true;
	}else{
		evf_change_error(obj,"add");
		evf_change_tip(obj,feed_back,"add");
		return false;
	}
}

function evf_change_tip(obj,msg,action_type){
	if(obj.attr("tip") == undefined){//初始化判断TIP是否为空
		obj.attr("is_tip_null","yes");
	}
	if(action_type == "add"){
		if(obj.attr("is_tip_null") == "yes"){
			obj.attr("tip",msg);
		}else{
			if(msg != null){
				if(obj.attr("tip_bak") == undefined){
					obj.attr("tip_bak",obj.attr("tip"));
				}
				obj.attr("tip",msg);
			}
		}
	}else{
		if(obj.attr("is_tip_null") == "yes"){
			obj.removeAttr("tip");
			obj.removeAttr("tip_bak");
		}else{
			obj.attr("tip",obj.attr("tip_bak"));
			obj.removeAttr("tip_bak");
		}
	}
}

function evf_change_error(obj,action_type){
	if(action_type == "add"){
		obj.addClass("evf_valid-failed");
	}else{
		obj.removeClass("evf_valid-failed");
	}
}

$.fn.evf_callback = function(msg,action_type,options){
	this.each(function(){
		if(action_type == "failed"){
			evf_change_error($(this),"add");
			evf_change_tip($(this),msg,"add");
		}else{
			evf_change_error($(this),"remove");
			evf_change_tip($(this),null,"remove");
		}
	});
};


// jq_passwordStrength  =======================================================================================

$.fn.passwordStrength = function(options){
	return this.each(function(){
		var that = this;that.opts = {};
		that.opts = $.extend({}, $.fn.passwordStrength.defaults, options);
		
		that.div = $(that.opts.targetDiv);
		that.defaultClass = that.div.attr('class');
		
		that.percents = (that.opts.classes.length) ? 100 / that.opts.classes.length : 100;
		 v = $(this).keyup(function(){
			if( typeof el == "undefined" )
				this.el = $(this);
			var s = getPasswordStrength (this.value);
			var p = this.percents;
			var t = Math.floor( s / p );			
			if( 100 <= s ) t = this.opts.classes.length - 1;	
			this.div.removeAttr('class').addClass( this.defaultClass ).addClass( this.opts.classes[ t ]);	
		})		
	});
	//获取密码强度
	function getPasswordStrength(H){
		var D=(H.length);
		if(D>5){
			D=5
		}
		var F=H.replace(/[0-9]/g,"");
		var G=(H.length-F.length);
		if(G>3){G=3}
		var A=H.replace(/\W/g,"");
		var C=(H.length-A.length);
		if(C>3){C=3}
		var B=H.replace(/[A-Z]/g,"");
		var I=(H.length-B.length);
		if(I>3){I=3}
		var E=((D*10)-20)+(G*10)+(C*15)+(I*10);
		if(E<0){E=0}
		if(E>100){E=100}
		return E
	}
};

$.fn.passwordStrength.defaults = {
	classes : Array('psc_is10','psc_is20','psc_is30','psc_is40','psc_is50','psc_is60','psc_is70','psc_is80','psc_is90','psc_isa0'),
	targetDiv : '#passwordStrengthDiv',
	cache : {}
}

$.passwordStrength = {};

/*
$.passwordStrength.getRandomPassword = function(size){
		var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		var size = size || 8;
		var i = 1;
		var ret = ""
		while ( i <= size ) {
			$max = chars.length-1;
			$num = Math.floor(Math.random()*$max);
			$temp = chars.substr($num, 1);
			ret += $temp;
			i++;
		}
		return ret;			
}	
//*/