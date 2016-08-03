/*** Pulg-In  Info
 * zoeDylan
 * ImgChange
 * UpdateTime:2014-04-30
 * Versions:1.0.1
 * Quote:jquery-1.10.2.js 
 * Info:
 *      1.重构核心模块
 *      2.解决页面使用2个以上插件产生数据冲突问题
 *      3.添加6种开关
 *      4.解决宽高无法使用百分比问题
 *      5.增加更多自定义参数（详情请参考默认参数）  
 *      6.可显示缩略图
 *      7.可控制图片等比例缩放
 * PS:  插件样式不是很好,不过界面精简、代码注释频繁,适合新手学习.
 * 
 * Copyright:此插件由zoeDylan纯手工编写,仅供学习和参考使用！
***/

(function ($) {
    $.zoeDylan_ImgChange = function (ele, options) {
        var//当前元素
         _eleThis = $(ele),
         //内部参数
         _settings = '',//动态指示图标
         _icon = '○●',//自动切换控制器
         _autoChange = null,//图片组
         _imgG = $(),//当前图片
         _nowI = $(),//上一张图片
         _topI = $(),//下一张图片 中间计算下一张图片位置
         _botI = $();//上一张图片 中间计算上一张图片位置

        //参数组合 $.extend(true, {}, def, options);
        //true,{}  用于插件初始化参数
        _settings = $.extend(true, {}, $.zoeDylan_ImgChange.def, options);

        //添加模版
        function AddTemplate() {
            var //图片
                imgTemp,//信息
                infoTemp,//控制器
                controlTemp;

            //移除元素样式，并添加图片框架
            _eleThis.removeClass().addClass('zd-imgChange').append('<div class="zd-imgChange-imgF"></div>');

            if (_settings.isInfo) {//是否开启信息显示  添加信息框架
                _eleThis.append('<div class="zd-imgChange-infoF"></div>');
            }
            if (_settings.isControl) {//是否开启控制器显示  添加图片框架
                _eleThis.append('<div class="zd-imgChange-controlF"></div>');
            }
            if (_settings.isThumbnail) {//是否开启控制器显示缩略图  添加缩略图框架
                _eleThis.append('<div class="zd-imgChange-thumbnailF"></div>');
            }

            //添加图片
            for (var i = 0; i < _settings.imgLinks.length; i++) { 
                //添加图片
                imgTemp = $(document.createElement('img'));
                imgTemp.attr({
                    'alt': _settings.imgTips[i] == undefined ? "" : _settings.imgTips[i],
                    'src': _settings.imgLinks[i],
                    'title': _settings.imgTips[i]
                });
                _eleThis.children('.zd-imgChange-imgF').append(imgTemp);
                if (_settings.isClick) {//开启图片点击事件 
                    _eleThis.children('.zd-imgChange-imgF').children('img:eq(' + i + ')').attr('onclick','document.location="'+_settings.imgClick[i]+'"');
                }
                if (_settings.isInfo) {//是否开启信息显示
                    //添加图片信息
                    infoTemp = $(document.createElement('span'));
                    infoTemp.html(_settings.imgCont[i]);
                    _eleThis.children('.zd-imgChange-infoF').append(infoTemp);
                }
                if (_settings.isControl) {//是否开启控制器显示
                    //添加控制器按钮
                    controlTemp = $(document.createElement('span'));
                    controlTemp.html(_icon[0]);
                    _eleThis.children('.zd-imgChange-controlF').append(controlTemp);
                }
                if (_settings.isThumbnail) {//是否开启控制器显示缩略图
                    _eleThis.children('.zd-imgChange-thumbnailF').append('<img src="'+_settings.imgLinks[i]+'" alt="'+_settings.imgTips[i]+'" />');
                }
            }
        }

        //添加样式
        function AddStyle() {
            _eleThis.css({
                'width': _settings.width,
                'height': _settings.height,
                'background': _settings.background,
                'color': _settings.color
            }).children('.zd-imgChange-imgF').css({
                'width': '100%',
                'height': '100%'
            });
            if (_settings.isConstrain) {//判断是否等比例缩放
                _eleThis.children('.zd-imgChange-imgF').children('img').css({
                    'width': _settings.width > _settings.height ? 'auto' : '100%',
                    'height': _settings.width > _settings.height ? '100%' : 'auto'
                });
            } else {
                _eleThis.children('.zd-imgChange-imgF').children('img').css({
                    'width': '100%',
                    'height': '100%'
                });
            }
            //图片组赋值
            _imgG = _eleThis.children('.zd-imgChange-imgF');
            //启用点击事件
            if (_settings.isClick) {
                _eleThis.children('.zd-imgChange-imgF').css('cursor', 'pointer');
            }
            //缩略图
            if (_settings.isThumbnail) { 
                _eleThis.children('.zd-imgChange-imgF').css({
                    'height': (parseFloat(_settings.height) / 100 * 80)
                });
                _eleThis.children('.zd-imgChange-thumbnailF').css({
                    'height': (parseFloat(_settings.height) / 100 * 20),
                    'width': ((parseFloat(_settings.height) / 100 * 18) + 10) * _settings.imgLinks.length
                });
                _eleThis.children('.zd-imgChange-infoF').css('bottom', (parseFloat(_settings.height) / 100 * 20));
                _eleThis.children('.zd-imgChange-thumbnailF').children('img').css('width', (parseFloat(_settings.height) / 100 * 18));
            }
        }

        //初始化
        function Initialize() {
            if (_settings.isInfo) {//是否开启信息显示 
            }
            if (_settings.isControl) {//是否开启控制器显示 
            }
            if (_settings.isThumbnail) {//是否开启控制器显示缩略图 
            }

            //当前图片
            _nowI = _imgG.children('img:eq(0)').css('z-index', '2').css({
                'top': '0',
                'left': '0'
            });
            //下一张图片 中间计算下一张图片位置
            _botI = _imgG.children('img:eq(1)').css('z-index', '1');
            //上一张图片 中间计算上一张图片位置
            _topI = _imgG.children('img:eq(' + (_imgG.children('img').length - 1) + ')').css('z-index', '1');
            //图片信息
            if (_settings.isInfo) {
                _eleThis.children('.zd-imgChange-infoF').children('span').fadeOut(0);
                _eleThis.children('.zd-imgChange-infoF').children('span:eq(0)').fadeIn(0);
            }

            //控制器
            if (_settings.isControl) {
                _eleThis.children('.zd-imgChange-controlF').children('span:eq(0)').html(_icon[1]);
            }
            //缩略图
            if (_settings.isThumbnail) {
                _eleThis.children('.zd-imgChange-thumbnailF').children('img:eq(0)').css('margin-top','0');
            }
        }

        //事件绑定
        function BindEvent() {
            //控制器点击
            _eleThis.children('.zd-imgChange-controlF').children('span').bind('click', function () {
                if (_nowI.index() != $(this).index()) {
                    ChangeSwitcher($(this).index(), '');
                }
            });
            //缩略图点击
            _eleThis.children('.zd-imgChange-thumbnailF').children('img').bind('click', function () {
                if (_nowI.index() != $(this).index()) {
                    ChangeSwitcher($(this).index(), '');
                }
            });
        }

        //自动切换
        function AutoChange() {
            if (_settings.isAutoChange) {
                ChangeSwitcher((_botI.index() > (_imgG.children('img').length - 1) ? 0 : _botI.index()), _settings.direction);
                _autoChange = setTimeout(AutoChange, _settings.timer);
            }
        }

        //切换效果  num=当前切换图片下标号  tblr=切换方向
        function ChangeSwitcher(num, tblr) {
            //结束自动切换计算
            clearTimeout(_autoChange);
            //立即完成上次动画
            _imgG.finish();
            //图片赋值到临时变量
            var
                tempNowI = _nowI,
                tempTopI = _topI,
                tempBotI = _botI;
            //重新赋值
            //当前图片
            _nowI = _imgG.children('img:eq(' + num + ')');
            //下一张图片 中间计算下一张图片位置 
            _botI = _imgG.children('img:eq(' + (num >= (_imgG.children('img').length - 1) ? 0 : (num + 1)) + ')');
            //上一张图片 中间计算上一张图片位置
            _topI = _imgG.children('img:eq(' + (num <= 0 ? (_imgG.children('img').length - 1) : (num - 1)) + ')');
            //确认层级
            _imgG.children('img').css('z-index', '0');
            _nowI.css('z-index', '2');
            _topI.css('z-index', '1');
            _botI.css('z-index', '1');

            //动画部分
            //根据方向使用动画  
            switch (tblr) {
                case 't':
                    _nowI.css({
                        'top': '100%',
                        'left': '0'
                    }).animate({
                        'top': '0'
                    }, _settings.speed);
                    _topI.animate({
                        'top': '-100%'
                    }, _settings.speed);
                    break;
                case 'b':
                    _nowI.css({
                        'top': '-100%',
                        'left': '0'
                    }).animate({
                        'top': '0'
                    }, _settings.speed);

                    _topI.animate({
                        'top': '100%'
                    }, _settings.speed);
                    break;
                case 'l':
                    _nowI.css({
                        'top': '0',
                        'left': '100%'
                    }).animate({
                        'left': '0'
                    }, _settings.speed);

                    _topI.animate({
                        'left': '-100%'
                    }, _settings.speed);
                    break;
                case 'r':
                    _nowI.css({
                        'top': '0',
                        'left': '-100%'
                    }).animate({
                        'left': '0'
                    }, _settings.speed);

                    _topI.animate({
                        'left': '100%'
                    }, _settings.speed);
                    break;
                default:
                    _nowI.fadeOut(0).css({
                        'top': '0',
                        'left': '0'
                    }).fadeIn(_settings.speed);
                    _topI.fadeOut(_settings.speed).css({
                        'left': '100%'
                    }).fadeIn(0);
                    break;
            }
            //图片信息
            if (_settings.isInfo) {
                _eleThis.children('.zd-imgChange-infoF').children('span').finish();
                _eleThis.children('.zd-imgChange-infoF').children('span:eq(' + _topI.index() + ')').fadeOut(_settings.speed);
                _eleThis.children('.zd-imgChange-infoF').children('span:eq(' + _nowI.index() + ')').fadeIn(_settings.speed);
            }

            //控制器
            if (_settings.isControl) {
                //控制器改变
                _eleThis.children('.zd-imgChange-controlF').children('span').html(_icon[0]);
                if (_settings.isControl) {
                    _eleThis.children('.zd-imgChange-controlF').children('span:eq(' + _nowI.index() + ')').html(_icon[1]);
                }
            }

            //缩略图
            if (_settings.isThumbnail) {
                var
                    thImgW = _eleThis.children('.zd-imgChange-thumbnailF').children('img:eq(0)').width();  
                //缩略图插件显示位置 
                if (((_nowI.index()+1) * (thImgW + 10)) > parseInt(_settings.width)) {
                    _eleThis.children('.zd-imgChange-thumbnailF').animate({ 'left': -(((_nowI.index() + 2) * (thImgW + 10)) - parseInt(_settings.width)) },_settings.speed);
                } else {
                    _eleThis.children('.zd-imgChange-thumbnailF').animate({'left': '0'},_settings.speed);
                }
                _eleThis.children('.zd-imgChange-thumbnailF').children('img').css('margin-top', '1%');
                _eleThis.children('.zd-imgChange-thumbnailF').children('img:eq('+_nowI.index()+')').css('margin-top', '0');
            }

            //自动切换判断
            if (_settings.isAutoChange) {
                _autoChange = setTimeout(AutoChange, _settings.timer);
            }
        }

        //启用开关 
        function PulgInRun() {
            AddTemplate();
            AddStyle();
            Initialize();
            BindEvent();
            // AutoChange();
            if (_settings.isAutoChange) {
                _autoChange = setTimeout(AutoChange, _settings.timer);
            }
        }
        PulgInRun();
    }

    //默认参数 插件定义的参数
    $.zoeDylan_ImgChange.def = { //背景色       
        background: 'none', //前景色
        color: '#fff', //高
        height: '300px',//宽
        width: '500px',//图片地址数组
        imgLinks: new Array(),//图片内容
        imgCont: new Array(),//图片提示
        imgTips: new Array(),//图片点击
        imgClick: new Array(),
        //是否等比例缩放
        isConstrain: false,//是否开启自动切换
        isAutoChange: false,//是否显示控制器
        isControl: false,//是否显示图片信息
        isInfo: false,//是否开启图片点击事件
        isClick: false,//是否显示缩略图控件
        isThumbnail: false,
        //自动切换时间(毫秒)
        timer: 3000,//切换速度(毫秒)
        speed: 300,//默认切换方向(暂定于：l r t b (大小写区分)  》》左 右 上 下)
        direction: ''
    }

    $.fn.zoeDylan_ImgChange = function (options) {
        return this.each(function () {
            (new $.zoeDylan_ImgChange(this, options));
        });
    };
})(jQuery);