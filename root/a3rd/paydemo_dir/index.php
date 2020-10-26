<?php
namespace imcat;
require __DIR__."/config.php";
?>
<!DOCTYPE html><html><head>
<title><?php lang('a3rd.demo_title',0); ?></title>
<meta charset="utf-8">
<style>
* { margin: 0; padding: 0; }
ul, ol { list-style: none; }
.title { color: #ADADAD; font-size: 14px; font-weight: bold; padding: 8px 16px 5px 10px; }
.hidden { display: none; }
.new-btn-login-sp { border: 1px solid #D74C00; padding: 1px; display: inline-block; }
.new-btn-login { background-color: #ff8c00; color: #FFFFFF; font-weight: bold; border: medium none; width: 82px; height: 28px; }
.new-btn-login:hover { background-color: #ffa300; width: 82px; color: #FFFFFF; font-weight: bold; height: 28px; }
.bank-list { overflow: hidden; margin-top: 5px; }
.bank-list li { float: left; width: 153px; margin-bottom: 5px; }
#main { width: 750px; margin: 0 auto; font-size: 14px; font-family: '宋体'; }
#logo { background-color: transparent; background-image: url("images/new-btn-fixed.png"); border: medium none; background-position: 0 0; width: 166px; height: 35px; float: left; }
.red-star { color: #f00; width: 10px; display: inline-block; }
.null-star { color: #fff; }
.content { margin-top: 5px; }
.content dt { width: 160px; display: inline-block; text-align: right; float: left; }
.content dd { margin-left: 100px; margin-bottom: 5px; }
#foot { margin-top: 10px; }
.foot-ul li { text-align: center; }
.note-help { color: #999999; font-size: 12px; line-height: 130%; padding-left: 3px; }
.cashier-nav { font-size: 14px; margin: 15px 0 10px; text-align: left; height: 30px; border-bottom: solid 2px #CFD2D7; }
.cashier-nav ol li { float: left; }
.cashier-nav li.current { color: #AB4400; font-weight: bold; }
.cashier-nav li.last { clear: right; }
.alipay_link { text-align: right; }
.alipay_link a:link { text-decoration: none; color: #8D8D8D; }
.alipay_link a:visited { text-decoration: none; color: #8D8D8D; }
</style>
</head>
<body text=#000000 bgColor=#ffffff leftMargin=0 topMargin=4>
<div id="main">
    <div id="head">
        <dl class="alipay_link">
            <a target="_blank" href="#"><span>Homepage</span></a>| <a target="_blank" href="#"><span>VIP Center</span></a>| <a target="_blank" href="#"><span>Help Center</span></a>
        </dl>
        <span class="title"><?php lang('a3rd.demo_title',0); ?></span> </div>
    <div class="cashier-nav">
        <ol>
            <li class="current">1. Step1 →</li>
            <li>2. Step2 →</li>
            <li class="last">3. Step3</li>
        </ol>
    </div>  

    <form name='demopayment' action='uapi.php' method='post' target="_blank">
        <div id="body" style="clear:left">
            <dl class="content">
                <dt><?php lang('a3rd.index_notitle',0); ?></dt>
                <dd> <span class="null-star">*</span>
                    <input name="out_trade_no" value="<?php echo basKeyid::kidTemp('(def)'); ?>" size="30" />
                    <span><?php lang('a3rd.index_tipno',0); ?></span> </dd>
                <dt><?php lang('a3rd.index_ordtitle',0); ?></dt>
                <dd> <span class="null-star">*</span>
                    <input name="subject" value="testName" size="30" />
                    <span><?php lang('a3rd.index_tipmust',0); ?> </span> </dd>
                <dt><?php lang('a3rd.index_ordamount',0); ?></dt>
                <dd> <span class="null-star">*</span>
                    <input name="total_fee" value="0.01" size="30" />
                    <span><?php lang('a3rd.index_tipmust',0); ?> </span> </dd>
                <dt><?php lang('a3rd.index_ordrem',0); ?></dt>
                <dd> <span class="null-star">*</span>
                    <input name="ordbody" value="testDescription" size="30" />
                    <span></span> </dd>
                <dt><?php lang('a3rd.index_ordurl',0); ?></dt>
                <dd> <span class="null-star">*</span>
                    <input name="show_url" value="http://www.domain.com/item/1234.php" size="30" />
                    <span>eg. http://www.my_domain.com/myorder.html </span> </dd>
                <dt></dt>
                <dd> <span class="new-btn-login-sp">
                    <button class="new-btn-login" type="submit" style="text-align:center;"><?php lang('a3rd.index_confirm',0); ?></button>
                    </span> </dd>
            </dl>
        </div>
    </form>
    <div id="foot">
        <ul class="foot-ul">
            <li><font class="note-help">Its JUST DEMO Pay Flow!</font></li>
            <li> Pay Demo Flow@2011-2016 </li>
        </ul>
    </div>
</div>
</body>
</html>