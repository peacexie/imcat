
var _stcssx = {css1:0, css2:0};

~(function($) {

    // 参考: https://www.cnblogs.com/zhangxiaoyong/p/6117765.html
    $.fn.picView = function(opts) {

// code1-start ----------------
var defs = {box:'vbox__412', css:'vcss__412', zind:4210}
var opts = $.extend(defs,opts); //console.log(opts);

function acss(opts){
    if(_stcssx.css1) return;
    var css = "\
        ."+opts.box+"{z-index:"+opts.zind+"; background-color:#000; opacity:.95; filter:alpha(opacity=70);\
        position:fixed; top:5%; left:5%; width:90%; height:90%; text-align:center; overflow:scroll;}\
        ."+opts.box+" p{color:#FFF; line-height:200%; margin:0.5rem;}."+opts.box+" p a{color:#9F9;cursor:pointer;}\
        ."+opts.css+"{z-index:"+(opts.zind+1)+"; width:auto; height:auto;cursor:pointer;} ";
    $("<style></style>").text(css).appendTo($("head"));
} // 多次调用,运行一次即可？
acss(opts);

function vone(imgs, oimg, no, opts) {
    var boxid = opts.box; 
    $(oimg).click(function(){
        $('#'+boxid).remove(); 
        // box
        var overlay = document.createElement("div");
        overlay.setAttribute("id", boxid);
        overlay.setAttribute("class", boxid);
        $('body').append(overlay);
        // Btn/title
        var p1 = document.createElement("p");
        $(p1).append('<a>[<前]</a> &nbsp; <a>返回</a> &nbsp; <a>[后>]</a>');
        var title = '<br>'+(no+1)+'/'+imgs.length;
        if($(oimg).prop('title')){
            title += ' : ' + $(oimg).prop('title'); 
        }
        $(p1).append(title);
        $('#'+boxid).append(p1);
        // prev/next
        var a1 = $(p1).find('a').eq(0),
            a2 = $(p1).find('a').eq(2),
            a0 = $(p1).find('a').eq(1);
        $(a1).bind('click', function(){ 
            var id = no-1; 
            if(id<0) id = imgs.length-1;
            $(po).find('img').eq(id).trigger('click');
        });
        $(a2).bind('click', function(){ 
            var id = no+1; 
            if(id>=imgs.length) id = 0;
            $(po).find('img').eq(id).trigger('click');
        });
        $(a0).bind('click', function(){ 
            $('#'+boxid).remove();
        });
        // img/act
        var img = document.createElement("img");
        img.setAttribute("class", opts.css);
        img.setAttribute("title", "点图片返回"); 
        img.src = oimg.getAttribute("src"); 
        $('#'+boxid).append(img);
        $(img).bind('click', function(){ 
            $('#'+boxid).remove();
        });
    });
}

var po = this,
    imgs = $(this).find('img'), 
    ilen = $(imgs).length;
return $(imgs).each(function(i, oimg){
    vone(imgs, oimg, i, opts);
});

};
// code1-end ----------------

    // =========================================

    $.fn.picPlay = function(opts) {

var defs = {li:'li', bin:'ppin_box', bout:'ppout',zind:4210}
var opts = $.extend(defs,opts);

function acss(opts){
    //if(_stcssx.css2) return;
    var css = "\
    ."+opts.bout+"{ clear:both; position:relative; overflow:hidden; display:block; }\
    ."+opts.bin+"{ position:relative; width:100%; }\
    ."+opts.bin+" li{ float:left; position:absolute; top:0; left:0; width:100%; }\
    ."+opts.bin+" li a{ display:block; width:100%;  height:100%; }\
    /*圆点*/\
    .cir_box{ overflow:hidden; position:absolute; z-index:"+opts.zind+"; }\
    .cir_box li{ float:left; width:10px; height:10px; border-radius:50%; margin:0 5px; cursor:pointer; background:rgba(255,255,255,.9); }\
    .cir_box .cir_on{background:rgba(232,56,54,.9) !important; }\
    /*按钮*/\
    .left_btn, .right_btn{ width:30px; height:60px; top:40%; line-height:60px; font-size:larger; text-align:center;\
      opacity:.8; filter:alpha(opacity=80); cursor:pointer; color:#FFC; background:#CCC; position:absolute; border-radius:5px; }\
    .left_btn{ float:left; left:0; border-top-left-radius:0; border-bottom-left-radius:0; }\
    .right_btn{ float:right; right:0; border-top-right-radius:0; border-bottom-right-radius:0; }\
    /*内容*/\
    ."+opts.bin+" span{height:34px; line-height:34px; background:rgba(0,0,0,0.5); color:#fff; position:absolute; bottom:0;\
      width:100%; height:34px; text-align:center; padding:0 10px; }\
    ."+opts.bin+" img { width:100%; height:100%; }";
    $("<style></style>").text(css).appendTo($("head"));
}
acss(opts);

        return this.each(function() {
// code2-start ----------------
var _play = $(this), _this = $(this), Li = opts.li;
var _box = _play.find("."+opts.bin);
var Over = "mouseover", Out = "mouseout", Click = "click";
var _cirBox = ".cir_box", cirOn = "cir_on", _cirOn = ".cir_on";
var cirlen = _box.children(Li).length; //圆点的数量/图片的数量
var _timer = 4e3, _swtime = 400; //轮播时间,图片切换时间
var _pw = $(_this).parent().innerWidth(), _ph = _pw * 3/4;
setWh(_this, opts, _pw, _ph); 
cir(); Btn();
// 设置高
function setWh(_this, opts, _pw, _ph) {
    $(_this).height(_ph);
    //$(_this).parent().height(_ph);
    $(_this).find(Li).height(_ph);
    $(_this).find(Li).find('a').height(_ph);
}
//根据图片的数量来生成圆点
function cir() { 
    _play.append('<ul class="cir_box"></ul>');
    var cir_box = _play.find(".cir_box");
    for (var i = 0; i < cirlen; i++) {
        cir_box.append('<li style="" value="' + i + '"></li>');
    }
    var cir_dss = cir_box.width();
    cir_box.css({
        left:"50%",
        marginLeft:-cir_dss / 2,
        bottom:"40px"
    });
    cir_box.children(Li).eq(0).addClass(cirOn);
}
//生成左右按钮
function Btn() {
    _play.append('<div class="ppx_btn"></div>');
    var _btn = _play.find(".ppx_btn");
    _btn.append('<div class="left_btn"><</div><div class="right_btn">></div>');
    var leftBtn = _btn.find(".left_btn");
    var rightBtn = _btn.find(".right_btn");
    //点击左面按钮
    leftBtn.bind(Click, function() {
        var cir_box = _this.find(_cirBox);
        var onLen = _this.find(_cirOn).val();
        _box.children(Li).eq(onLen).stop(false, false).animate({
            opacity:0
        }, _swtime).siblings().css("display", "block");
        if (onLen == 0) {
            onLen = cirlen;
        }
        _box.children(Li).eq(onLen - 1).stop(false, false).animate({
            opacity:1
        }, _swtime).siblings().css("display", "none");
        cir_box.children(Li).eq(onLen - 1).addClass(cirOn).siblings().removeClass(cirOn);
    });
    //点击右面按钮
    rightBtn.bind(Click, function() {
        var cir_box = _this.find(_cirBox);
        var onLen = _this.find(_cirOn).val();
        _box.children(Li).eq(onLen).stop(false, false).animate({
            opacity:0
        }, _swtime).siblings().css("display", "block");
        if (onLen == cirlen - 1) {
            onLen = -1;
        }
        _box.children(Li).eq(onLen + 1).stop(false, false).animate({
            opacity:1
        }, _swtime).siblings().css("display", "none");
        cir_box.children(Li).eq(onLen + 1).addClass(cirOn).siblings().removeClass(cirOn);
    });
}
//定时器
var int = setInterval(clock, _timer);
function clock() {
    var cir_box = _this.find(_cirBox);
    var onLen = _this.find(_cirOn).val();
    _box.children(Li).eq(onLen).stop(false, false).animate({
        opacity:0
    }, _swtime).siblings().css("display", "block");
    if (onLen == cirlen - 1) {
        onLen = -1;
    }
    _box.children(Li).eq(onLen + 1).stop(false, false).animate({
        opacity:1
    }, _swtime).siblings().css("display", "none");
    cir_box.children(Li).eq(onLen + 1).addClass(cirOn).siblings().removeClass(cirOn);
}
// 鼠标在图片上 关闭定时器
_play.bind(Over, function() {
    jQuery(this).find(".left_btn,.right_btn").show();
    clearTimeout(int);
});
_play.bind(Out, function() {
    jQuery(this).find(".left_btn,.right_btn").hide();
    int = setInterval(clock, _timer);
});
//鼠标划过圆点 切换图片
_this.find(_cirBox).children(Li).bind(Over, function() {
    var inde = jQuery(this).index();
    jQuery(this).addClass(cirOn).siblings().removeClass(cirOn);
    _box.children(Li).stop(false, false).animate({
        opacity:0
    }, _swtime).siblings().css("display", "block");
    _box.children(Li).eq(inde).stop(false, false).animate({
        opacity:1
    }, _swtime).siblings().css("display", "none");
});
// code2-end ----------------
        });
    };

})(jQuery);

/*
  $('#imgp1').picView();
  $('#imgp2').picView();
  $(".ppout").picPlay({li:'li',inbox:'ppin_box'});
*/
