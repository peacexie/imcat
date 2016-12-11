
// win-tips.js
// win_poplayer
// win_webox.js

// *** tipsWindown - jQuery弹出窗口 By Await [2009-11-22] ================================================
/*参数：[可选参数在调用时可写可不写,其他为必写]
    title:	窗口标题
  content:  内容(可选内容为){ text | id | img | url | iframe }
    width:	内容宽度
   height:	内容高度
	 drag:  是否可以拖动(ture为是,false为否)
     time:	自动关闭等待的时间，为空是则不自动关闭
   showbg:	[可选参数]设置是否显示遮罩层(0为不显示,1为显示)
  cssName:  [可选参数]附加class名称
     示例:  simpleWindown("例子","text:例子","500","400","true","3000","0","exa")
*/
var showWindown = true;
var templateSrc = "http://www.txmao.com"; //设置loading.gif路径
function tipsWindown(title,content,width,height,drag,time,showbg,cssName,backcall) {
	$("#wtips-box").remove(); //请除内容
	var width = width>= 960 ? this.width=960 : this.width=width;	    //设置最大窗口宽度
	var height = height>= 640 ? this.height=640 : this.height=height;  //设置最大窗口高度
	if(showWindown == true) {
		var simpleWindown_html = new String;
			simpleWindown_html = "<div id=\"wtips-bg\" style=\"height:"+$(document).height()+"px;filter:alpha(opacity=0);opacity:0;z-index: 999901\"></div>";
			simpleWindown_html += "<div id=\"wtips-box\">";
			simpleWindown_html += "<div id=\"wtips-title\"><h2></h2><span id=\"wtips-close\">[X]</span></div>";
			simpleWindown_html += "<div id=\"wtips-content-border\"><div id=\"wtips-content\"></div></div>"; 
			simpleWindown_html += "</div>";
			$("body").append(simpleWindown_html);
			show = false;
	}
	contentType = content.substring(0,content.indexOf(":"));
	content = content.substring(content.indexOf(":")+1,content.length);
	switch(contentType) {
		case "text":
		$("#wtips-content").html(content);
		break;
		case "id":
		$("#wtips-content").html($("#"+content+"").html());
		break;
		case "img":
		$("#wtips-content").ajaxStart(function() {
			$(this).html("<img src='"+templateSrc+"/images/loading.gif' class='loading' />");
		});
		$.ajax({
			error:function(){
				$("#wtips-content").html("<p class='wtips-error'>Load Data Error...</p>");
			},
			success:function(html){
				$("#wtips-content").html("<img src="+content+" alt='' />");
			}
		});
		break;
		case "url":
		var content_array=content.split("?");
		$("#wtips-content").ajaxStart(function(){
			$(this).html("<img src='"+templateSrc+"/images/loading.gif' class='loading' />");
		});
		$.ajax({
			type:content_array[0],
			url:content_array[1],
			data:content_array[2],
			error:function(){
				$("#wtips-content").html("<p class='wtips-error'>Load Data Error...</p>");
			},
			success:function(html){
				$("#wtips-content").html(html);
				if(backcall)
					backcall();
			}
		});
		break;
		case "iframe":
		$("#wtips-content").ajaxStart(function(){
			$(this).html("<img src='"+templateSrc+"/images/loading.gif' class='loading' />");
		});
		$.ajax({
			error:function(){
				$("#wtips-content").html("<p class='wtips-error'>Load Data Error...</p>");
			},
			success:function(html){
				$("#wtips-content").html("<iframe src=\""+content+"\" width=\"100%\" height=\""+parseInt(height)+"px"+"\" frameborder=\"0\" marginheight=\"0\" marginwidth=\"0\" style='overflow-y:hidden;overflow-x:hidden;'></iframe>");
			}
		});
	}
	$("#wtips-title h2").html(title);
	if(showbg == "true") {$("#wtips-bg").show();}else {$("#wtips-bg").remove();};
	$("#wtips-bg").animate({opacity:"0.5"},"normal");//设置透明度
	$("#wtips-box").show();
	if( height >= 527 ) {
		$("#wtips-title").css({width:(parseInt(width)+22)+"px"});
		$("#wtips-content").css({width:(parseInt(width)+17)+"px",height:height+"px"});
	}else {
		$("#wtips-title").css({width:(parseInt(width)+10)+"px"});
		$("#wtips-content").css({width:width+"px",height:height+"px"});
	}
	var	cw = document.documentElement.clientWidth,ch = document.documentElement.clientHeight,est = document.documentElement.scrollTop; 
	$("#wtips-box").css({left:"50%",top:"50%",marginTop:-((parseInt(height)+53)/2)+"px",marginLeft:-((parseInt(width)+32)/2)+"px",zIndex: "999999"}); 
	var Drag_ID = document.getElementById("wtips-box"),DragHead = document.getElementById("wtips-title");
		
	var moveX = 0,moveY = 0,moveTop,moveLeft = 0,moveable = false;
		moveTop = 0;
	var	sw = Drag_ID.scrollWidth,sh = Drag_ID.scrollHeight;
		DragHead.onmouseover = function(e) {
			if(drag == "true"){DragHead.style.cursor = "move";}else{DragHead.style.cursor = "default";}
		};
		DragHead.onmousedown = function(e) {
		if(drag == "true"){moveable = true;}else{moveable = false;}
		e = window.event?window.event:e;
		var ol = Drag_ID.offsetLeft, ot = Drag_ID.offsetTop-moveTop;
		moveX = e.clientX-ol;
		moveY = e.clientY-ot;
		document.onmousemove = function(e) {
			if (moveable) {
				try{ document.selection.empty(); }
				catch(e){ window.getSelection().removeAllRanges();}
				e = window.event?window.event:e;
				var x = e.clientX - moveX;
				var y = e.clientY - moveY;
				//if ( x > 0 &&( x + sw < cw) && y > 0 && (y + sh < ch) ) {
					Drag_ID.style.left = x + "px";
					Drag_ID.style.top = parseInt(y+moveTop) + "px";
					Drag_ID.style.margin = "auto";
				//}
			}
		}
		document.onmouseup = function () {moveable = false;};
		Drag_ID.onselectstart = function(e){return false;}
	}
	$("#wtips-content").attr("class","wtips-"+cssName);
	if( time == "" || typeof(time) == "undefined") {
		$("#wtips-close").click(function() {
			//showselect('t123_')
			$("#wtips-bg").remove();
			$("#wtips-box").fadeOut("slow",function(){$(this).remove();});
		});
	}else { 
		setTimeout(closeWindown,time);
	}
	//hideselect('t123_');
}
var closeWindown = function() {
	//showselect('t123_');
	$("#wtips-bg").remove();
	$("#wtips-box").fadeOut("slow",function(){$(this).remove();});
}

// *** win_PopupLayer ====================================================================================

Function.prototype.binding = function() {
    if (arguments.length < 2 && typeof arguments[0] == "undefined") return this;
    var __method = this, args = jQuery.makeArray(arguments), object = args.shift();
    return function() {
        return __method.apply(object, args.concat(jQuery.makeArray(arguments)));
    }
}

var Class = function(subclass){
	subclass.setOptions = function(options){
		this.options = jQuery.extend({}, this.options,options);
		for(var key in options){
			if(/^on[A-Z][A-Za-z]*$/.test(key)){
				$(this).on(key,'',options[key]);
			}
		}
	}
    var fn =  function(){
        if(subclass._init && typeof subclass._init == 'function'){
            this._init.apply(this,arguments);
        }
    }
    if(typeof subclass == 'object'){
        fn.prototype = subclass;
    }
    return fn;
}

var PopupLayer = new Class({
	options:{
		trigger:null,                            //触发的元素或id,必填参数
		popupBlk:null,                           //弹出内容层元素或id,必填参数
		closeBtn:null,                           //关闭弹出层的元素或id
		popupLayerClass:"popupLayer",            //弹出层容器的class名称
		eventType:"click",                       //触发事件的类型
		offsets:{                                //弹出层容器位置的调整值
			x:0,
			y:0
		},
		useFx:false,                             //是否使用特效
		useOverlay:false,                        //是否使用全局遮罩
		usePopupIframe:true,                     //是否使用容器遮罩
		isresize:true,                           //是否绑定window对象的resize事件
		onBeforeStart:function(){}            //自定义事件
	},
	_init:function(options){
		this.setOptions(options);                //载入设置
		this.isSetPosition = this.isDoPopup = this.isOverlay = true;    //定义一些开关
		this.popupLayer = $(document.createElement("div")).addClass(this.options.popupLayerClass);     //初始化最外层容器
		this.trigger = $(this.options.trigger);                         //把触发元素封装成实例的一个属性，方便使用及理解
		this.popupBlk = $(this.options.popupBlk);                       //把弹出内容层元素封装成实例的一个属性
		this.closeBtn = $(this.options.closeBtn);                       //把关闭按钮素封装成实例的一个属性
		$(this).trigger("onBeforeStart");                               //执行自定义事件。
		this._construct()                                               //通过弹出内容层，构造弹出层，即为其添加外层容器及底层iframe
		this.trigger.on(this.options.eventType,'',function(){            //给触发元素绑定触发事件
			if(this.isSetPosition){
				this.setPosition(this.trigger.offset().left + this.options.offsets.x, this.trigger.offset().top + this.trigger.get(0).offsetHeight + this.options.offsets.y);
			}
			this.options.useOverlay?this._loadOverlay():null;               //如果使用遮罩则加载遮罩元素
			(this.isOverlay && this.options.useOverlay)?this.overlay.show():null;
			if(this.isDoPopup && (this.popupLayer.css("display")== "none")){
				this.options.useFx?this.doEffects("open"):this.popupLayer.show();
			}							 
		}.binding(this));
		this.isresize?$(window).on("resize",'',this.doresize.binding(this)):null;
		this.options.closeBtn?this.closeBtn.on("click",'',this.close.binding(this)):null;   //如果有关闭按钮，则给关闭按钮绑定关闭事件
	},
	_construct:function(){                  //构造弹出层
		this.popupBlk.show(); 
		this.popupLayer.append(this.popupBlk.css({opacity:1})).appendTo($(document.body)).css({position:"absolute",'z-index':2,width:this.popupBlk.get(0).offsetWidth,height:this.popupBlk.get(0).offsetHeight});
		this.options.usePopupIframe?this.popupLayer.append(this.popupIframe):null;
		this.popupLayer.hide();
	},
	_loadOverlay:function(){                //加载遮罩
		pageWidth = ($.browser.version=="6.0")?$(document).width()-21:$(document).width();
		this.overlay?this.overlay.remove():null;
		this.overlay = $(document.createElement("div"));
		this.overlay.css({position:"absolute","z-index":1,left:0,top:0,zoom:1,display:"none",width:pageWidth,height:$(document).height()}).appendTo($(document.body)).append("<div style='position:absolute;z-index:2;width:100%;height:100%;left:0;top:0;opacity:0.3;filter:Alpha(opacity=30);background:#000'></div><iframe frameborder='0' border='0' style='width:100%;height:100%;position:absolute;z-index:1;left:0;top:0;filter:Alpha(opacity=0);'></iframe>")
	},
	doresize:function(){
		this.overlay?this.overlay.css({width:($.browser.version=="6.0")?$(document).width()-21:$(document).width(),height:($.browser.version=="6.0")?$(document).height()-4:$(document).height()}):null;
		if(this.isSetPosition){
			this.setPosition(this.trigger.offset().left + this.options.offsets.x, this.trigger.offset().top + this.trigger.get(0).offsetHeight + this.options.offsets.y);
		}
	},
	setPosition:function(left,top){          //通过传入的参数值改变弹出层的位置
		this.popupLayer.css({left:left,top:top});
	},
	doEffects:function(way){                //做特效
		way == "open"?this.popupLayer.show("slow"):this.popupLayer.hide("slow");
		
	},
	close:function(){                      //关闭方法
		this.options.useOverlay?this.overlay.hide():null;
		this.options.useFx?this.doEffects("close"):this.popupLayer.hide();
	}
});

/***** Jquery.webox_out ======================================================================================
 *	Plugin: Jquery.webox_out
 *	Developer: Blank
 *	Version: 1.0 Beta
 *	Update: 2012.07.08
**/
$.extend({
	webox:function(option){
		var _x,_y,m,allscreen=false;
		if(!option){
			alert('options can\'t be empty');
			return;
		};
		if(!option['html']&&!option['iframe']){
			alert('html attribute and iframe attribute can\'t be both empty');
			return;
		};
		option['parent']='webox';
		option['locked']='locked';
		$(document).ready(function(e){
			$('.webox_out').remove();
			$('.webox_bg').remove();
			var width=option['width']?option['width']:640;
			var height=option['height']?option['height']:480;
			$('body').append('<div class="webox_bg" style="display:none;"></div><div class="webox_out" style="width:'+width+'px;height:'+height+'px;display:none;"><div id="webox_in" style="height:'+height+'px;"><h1 id="locked" onselectstart="return false;">'+(option['title']?option['title']:'webox')+'<a class="span" id="webox_close" href="javascript:void(0);"></a></h1>'+(option['iframe']?'<iframe class="w_iframe" src="'+option['iframe']+'" frameborder="0" width="100%" scrolling="yes" style="border:none;overflow-x:hidden;height:'+parseInt(height-30)+'px;"></iframe>':option['html']?option['html']:'')+'</div></div>');
			if(option['bgvisibel']){
				$('.webox_bg').fadeTo('slow',0.3);
			};
			$('.webox_out').css({display:'block'});
			$('#'+option['locked']+' .span').click(function(){
				$('.webox_out').css({display:'none'});
				$('.webox_bg').css({display:'none'});
			});
			var marginLeft=parseInt(width/2);
			var marginTop=parseInt(height/2);
			var winWidth=parseInt($(window).width()/2);
			var winHeight=parseInt($(window).height()/2.2);
			var left=winWidth-marginLeft; if(left<2) left = 2;
			var top=winHeight-marginTop; if(top<2) top = 2;
			$('.webox_out').css({left:left,top:top});
			//* 拖动,双击事件
			$('#'+option['locked']).mousedown(function(e){
				if(e.which){
					m=true;
					_x=e.pageX-parseInt($('.webox_out').css('left'));
					_y=e.pageY-parseInt($('.webox_out').css('top'));
				}
			}).dblclick(function(){
					if(allscreen){
						$('.webox_out').css({height:height,width:width});
						$('#webox_in').height(height);
						$('.w_iframe').height(height-30);
						$('.webox_out').css({left:left,top:top});
						allscreen = false;
					}else{
						allscreen=true;
						var screenHeight = $(window).height();
						var screenWidth = $(window).width();$
						('.webox_out').css({'width':screenWidth-18,'height':screenHeight-18,'top':'0px','left':'0px'});
						$('#webox_in').height(screenHeight-20);
						$('.w_iframe').height(screenHeight-50);
					}
			});
		}).mousemove(function(e){
			if(m && !allscreen){
				var x=e.pageX-_x;
				var y=e.pageY-_y;
				$('.webox_out').css({left:x});
				$('.webox_out').css({top:y});
				try{ document.selection.empty(); }
				catch(e){ window.getSelection().removeAllRanges();}
			}
		}).mouseup(function(){
				m=false; //*/
		});
		/*$(window).resize(function(){
			if(allscreen){
				var screenHeight = $(window).height();
				var screenWidth = $(window).width();
				$('.webox_out').css({'width':screenWidth-18,'height':screenHeight-18,'top':'0px','left':'0px'});
				$('#webox_in').height(screenHeight-20);
				$('.w_iframe').height(screenHeight-50);
			}
		});	*/
	}
});
