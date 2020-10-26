
<style type="text/css">
h3 { text-align:center; font-size:16px; color:#333; margin:1rem; }
.Ptable td{ padding:0.2rem; }
</style>

<ul class="tc ma10">
    <a href="?cargo-gbimp">政府采购：标准商品导入</a>
    #
    <a href="?cargo-ecimp">ECSHOP：商品导入</a>
</ul>

<script src="<?=PATH_VIEWS?>/adm/assets/govbuy.js"></script>
<?php if($mkv=='cargo-gbimp'){ ?>

<h3>政府采购：标准商品导入（对接阳光公采API）</h3>
<ul class="tc ma10">
    <a href="?<?=$mkv?>&act=list">xls文件列表</a> - 
    <a href="?<?=$mkv?>&act=up">上传xls文件</a> ● 
    <a href="http://zxpt.gdgpo.gov.cn/gdgpms/" target="_blank">省采平台</a>
</ul>
<p><b>操作步骤：</b></p>
<ol>
    <li>省采平台：导出标准商品(xls)并上传xls文件。 </li>
    <li>本地系统：创建检查如下目录：/xvars/gbatts, /xvars/gbdown。 </li>
    <li>上传xls文件后，会在以[xls文件列表]显示；点击文件，选取商品项，依次按如下操作： </li>
    <!--li>检查配件属性(缺失的请导入)，导入配件项目(已导入忽略)；</li-->
    <li>检查主体产品属性(缺失的请导入)，导入主体产品项目(已导入忽略)；</li>
    <li>本地系统后台：修改主产品(栏目,价格,重量,图片等)；</li>
    <li>省采平台：同步等操作。</li>
</ol>

<?php }else{ ?>

<h3>ECSHOP：商品导入（感谢ECSHOP这位`巨人`）</h3>
<ul class="tc ma10">
    <a href="?<?=$mkv?>&act=cfgs">检查配置</a> - 
    <a href="?<?=$mkv?>&act=test">idTest</a> <br>
    <a href="?<?=$mkv?>&act=immod">商品类型</a> - 
    <a href="?<?=$mkv?>&act=imcat">商品栏目</a> - 
    <a href="?<?=$mkv?>&act=impro">导入产品</a> - 
    <a href="?<?=$mkv?>&act=impic">导入图片</a> -
    <a href="?<?=$mkv?>&act=impart">导入配件</a>
</ul>
<p><b>操作步骤：</b></p>
<ol>
    <li>手动添加缓存目录：{DIR_VARS} . "/ecshop"</li>
    <li>手动运行sql：ALTER TABLE `ecs_goods` ADD `imflag` tinyint NOT NULL DEFAULT '0' AFTER `goods_name`;</li>
    <li>一些操作已经屏蔽，请自行调试。</li>
</ol>

<?php } ?>


