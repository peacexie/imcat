
;(function($){
    $.fn.lunbo=function(options){
        // var defaults={} //通过覆盖来传参数
        // var options=$.extend(defaults,options);
        return this.each(function(){
            var _lubo=$(this), _this=$(this);
            var _box=_lubo.find('.cgun_box');
            var luboHei=_box.height();
            var Over='mouseover', Out='mouseout';
            var Click='click', Li="li";
            var _cirBox='.cir_box', cirOn='cir_on', _cirOn='.cir_on';
            var cirlen=_box.children(Li).length; //圆点的数量  图片的数量
            var luboTime=4000, switchTime=400; //轮播时间,图片切换时间
            cir(); Btn();
            //根据图片的数量来生成圆点
            function cir(){
                _lubo.append('<ul class="cir_box"></ul>');
                var cir_box=_lubo.find('.cir_box');
                for(var i=0; i<cirlen;i++){cir_box.append('<li style="" value="'+i+'"></li>');}
                var cir_dss=cir_box.width(); 
                cir_box.css({top:'1rem', left:'1rem'}); 
                cir_box.children(Li).eq(0).addClass(cirOn);
            }
            //生成左右按钮
            function Btn(){
                _lubo.append('<div class="cgun_btn"></div>');
                var _btn=_lubo.find('.cgun_btn');
                _btn.append('<div class="left_btn"></div><div class="right_btn"></div>');
                var leftBtn=_btn.find('.left_btn');
                var rightBtn=_btn.find('.right_btn');
                //点击左面按钮
                leftBtn.bind(Click,function(){
                    var cir_box=_this.find(_cirBox);
                    var onLen=_this.find(_cirOn).val();
                    _box.children(Li).eq(onLen).stop(false,false).animate({
                        opacity:0
                    },switchTime).siblings().css("display","block");
                    if(onLen==0){onLen=cirlen;}
                    _box.children(Li).eq(onLen-1).stop(false,false).animate({
                        opacity:1
                    },switchTime).siblings().css("display","none");
                    cir_box.children(Li).eq(onLen-1).addClass(cirOn).siblings().removeClass(cirOn);
                })
                //点击右面按钮
                rightBtn.bind(Click,function(){
                    var cir_box=_this.find(_cirBox);
                    var onLen=_this.find(_cirOn).val();
                    _box.children(Li).eq(onLen).stop(false,false).animate({
                        opacity:0
                    },switchTime).siblings().css("display","block");
                    if(onLen==cirlen-1){onLen=-1;}
                    _box.children(Li).eq(onLen+1).stop(false,false).animate({
                        opacity:1
                    },switchTime).siblings().css("display","none");
                    cir_box.children(Li).eq(onLen+1).addClass(cirOn).siblings().removeClass(cirOn);
                })
            }
            //定时器
            var int = setInterval(clock,luboTime);
            function clock(){
                var cir_box=_this.find(_cirBox); // $(_cirBox);
                var onLen=_this.find(_cirOn).val(); // $(_cirOn).val();
                _box.children(Li).eq(onLen).stop(false,false).animate({
                    opacity:0
                },switchTime).siblings().css("display","block");
                if(onLen==cirlen-1){onLen=-1;}
                _box.children(Li).eq(onLen+1).stop(false,false).animate({
                    opacity:1
                },switchTime).siblings().css("display","none");
                cir_box.children(Li).eq(onLen+1).addClass(cirOn).siblings().removeClass(cirOn);
            }
            // 鼠标在图片上 关闭定时器
            _lubo.bind(Over,function(){
                jQuery(this).find('.left_btn,.right_btn').show();
                clearTimeout(int);
            });
            _lubo.bind(Out,function(){
                jQuery(this).find('.left_btn,.right_btn').hide();
                int=setInterval(clock,luboTime);
            });
            //鼠标划过圆点 切换图片
            _this.find(_cirBox).children(Li).bind(Over,function(){ // $(_cirBox)
                var inde = jQuery(this).index();
                jQuery(this).addClass(cirOn).siblings().removeClass(cirOn);
                _box.children(Li).stop(false,false).animate({
                    opacity:0
                },switchTime).siblings().css("display","block");
                _box.children(Li).eq(inde).stop(false,false).animate({
                    opacity:1
                },switchTime).siblings().css("display","none");
            });

        });
    }
})(jQuery);
