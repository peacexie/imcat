<?php
require(dirname(__FILE__).'/_config.php'); 
glbHtml::page('Types Pick',1);
glbHtml::page('imin'); //adm 
?>
<style TYPE="text/css">
body, td { background:#FFF; font-size:13px;}
table {border:1px #CCC solid; margin:auto;}
td {width:8px;height:8px;border:1px #FFF solid;}
td.tab td{ font-size:8px;line-height:8px;cursor:pointer; }
td.brd {border-right:1px solid #333;border-bottom:1px solid #333;border-top:1px solid #CCC;border-left:1px solid #CCC;}
#resDemo { width:40%; height:15px; border:1px #CCC solid; }
table.brd0{ border:0px; }
input { width:60px; margin:2px; }
button {width:5em; margin:2px 1px; }
body {margin:5px 5px;padding:0px;}
</style>
<script>

var tabHex1 = new Array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
var tabHex2 = new Array('00','33','66','99','CC','FF');
var tabC216 = new Array(); var n = 0;

for(i=0;i<6;i++){
for(j=0;j<6;j++){
for(k=0;k<6;k++){
	tabC216[n] = tabHex2[i]+tabHex2[j]+tabHex2[k];
	n++; //document.writeln(' ,'+tabC216[n]);
}}}
// 显示一个单元格
function cCell(id,rgb){
	var evt = ' onMouseover="cOver(this)" onClick="cClick(\''+id+'\',this)" ';
	return '<td style="background-color:#'+rgb+'" title='+rgb+' '+evt+'>&nbsp;</td>';
}
// tab216模型
function cTab1(row,col){
  var n = 0;
  for(i=0;i<row;i++){
	document.writeln('<tr>');
	for(j=0;j<col;j++){
	   document.write(cCell(1,tabC216[n]));
	   n++;
	}
	document.write('</tr>');
  }
}
// tab (颜色立方体 调色板) 
//  每行18种颜色,一共12行,上面6行和下面6行循环规律一样,不同的是 
//  RGU颜色表示法中R的初始值不同.所以程序需要在每行结束时处理循环中变量的值,在前6行结束时 
//  初始化后6行循环的初始值.这样做带来的问题是 循环不能结束.所以必须在颜色表颜色数量达到规定 
//  个数时改变变量使循环结束. 
function cTab2(){ 
	var str=""; str += "<tr>"; 
	var i=1,R="",G="",U=""; 
	for (l=0;l<=255;l=l+51){  //列内 0-255 循环 2 次 
		for (j=0;j<=255;j=j+51){  //前6行 0-102 循环,后6行 153-255 循环 
		for (k=0;k<=255;k=k+51){  //行内 0-255 循环 
				R=toHex(j); G=toHex(k); U=toHex(l); 
				if (R.length==1){R="0"+R;} 
				if (G.length==1){G="0"+G;} 
				if (U.length==1){U="0"+U;} 
				if ((i%18)==0){j=0;} 
				str += cCell(2,R+G+U); 
				if ((i%18)==0){l=l+51;if(i<109){j=-51;}
				else{j=102;}if(i!=216){str += "</tr><tr>";}
				if(i==108){j=153;k=-51;l=0;}
			} 
			i++; 
			if (i==217){j=256;k=256;l=256;}  //共216种颜色.达到数量后终止循环 
	}}} 
	document.write(str);
} 
// (连续色调 调色板) 
function cTab3(){ 
	var str = ""; str += "<tr>"; 
	var i=1,R="",G="",U="";  
	var O=1; //表示行内序号 
	for (k=255;k>=0;k=k-51){  //行内 不变 列内  FF-00 后半截 00-FF 
	for (j=204;j>=0;j=j-102){  //行内 FF-00 先正序 
	for (l=255;l>=0;l=l-51){  //行内 不变 列内  FF-00 后半截 00-FF 
		R=toHex(j); 
		if (i>108) {G=toHex(255-k);} else {G=toHex(k);} 
		if(O>6&&O<13){U=toHex(255-l);} else {U=toHex(l);} 
		if (R.length==1){R="0"+R;} 
		if (G.length==1){G="0"+G;} 
		if (U.length==1){U="0"+U;} 
		if ((i%18)==0){j=0;} 
		str += cCell(3,R+G+U);
		if((i%18)==0){k=k-51;O=0;if(i<109){j=306;}
		else{j=357}
		if(i!=216){str += "</tr><tr>";}
		if(i==108){j=255;k=255;l=306;}} 
		i++; O++; 
		if (i==217){j=-1;k=-1;l=-1;}  //共216种颜色.达到数量后终止循环 
	}}}  
	document.write(str);
}
// (灰度 调色板) 
// FFFFFF(16777215)-000000(0)的一个循环,每次循环减去 65793 
// 与前两种不同的是 每行 21 种 色彩  
function cTaba(){ 
	var str = ""; str += "<tr>"; 
	var L=1,RGU=""; 
	for (i=16777215;i>=0;i=i-65793) { 
		RGU=toHex(i); 
		if (RGU.length==5){RGU="0"+RGU;} 
		else if (RGU.length==4){RGU="00"+RGU;} 
		else if (RGU.length==3){RGU="000"+RGU;} 
		else if (RGU.length==2){RGU="0000"+RGU;} 
		else if (RGU.length==1){RGU="00000"+RGU;} 
		str += cCell('a',RGU);
		if ((L%21)==0){if(L!=217){str += "</tr><tr>";}} 
		L++; 
	} 
	str += "<td colspan=\"17\" bgcolor=\"#D6D3CE\"></td>"; 
	document.write(str);
} 
// 灰度,纯色
function cTab6(){ 
	var n=0, q=0;
	var tabm = new Array('xx0000','00xx00','0000xx','00xxxx','xx00xx','xxxx00');
	var tabc = new Array();for(i=0;i<6;i++){tabc[i] = tabHex2[5-i];}
	for(i=0;i<6;i++){ q++;
		c = tabHex2[i]+tabHex2[i]+tabHex2[i]; 
		document.write(cCell(6,c));
	}
	var k = 0;
	for(i=0;i<6;i++){
		var m0 = tabm[i]; 
		for(j=0;j<5;j++){ q++;
			var c0 = tabc[j] ;
			c = m0.replace('xx',c0).replace('xx',c0);
			document.write(cCell(6,c));
			if(q==18) document.write('</tr><tr>');
		}
	}
} 

function btnSEnd(type){ 
  var re = resCode.value;
  if(type=='Cancel') re = '';
  if(parDoc){
  	jsElm.pdID(pcolor).value = re.replace('#','');
  	jsElm.pdID(ptitle).style.color = re;
  	jsElm.pdID(pcolor+'_pop').style.display = 'none';
  }else{
    window.returnValue = '#'+re; 
    window.close(); //alert(re);
  }
}

function setBorder(tid){ 
  tds = jsElm.jeID('tab'+(tid=='6' ? '6'  :'x')).getElementsByTagName('td');
  for(var i=0;i<tds.length;i++){
    tds[i].className = 'c';
  }
}
function setColor(v){ 
  resCode.value = '#'+v;
  resDemo.style.background = '#'+v; //backgroundColor
}
function cOut(){ 
  setColor(resRGB.replace('#',''));
}
function cOver(e){ 
  setColor(e.title);
}
function cClick(id,e){ 
  resRGB = e.title;
  setColor(resRGB);
  setBorder(id);
  e.className = 'c brd';  
}

var _tab = '3'; // 3,2,1

</script>
<?php glbHtml::page('body'); ?>
<table border="0" align="center" cellpadding="0" cellspacing="1">  
  <tr>
    <td class="tab"><!--tab-->
      <table width="230" border="0" cellpadding="0" cellspacing="0" id=tabx onMouseOut="cOut()" align="center">
        <script>
		if(_tab=='1'){cTab1(6,36)}else if(_tab=='2'){cTab2()}else{cTab3()}
        </script>
      </table>
    </td>
  </tr>
  <tr>
    <td class="tab"><!--tab-->
      <table width="230" border="0" cellpadding="0" cellspacing="0" id=tab6 onMouseOut="cOut()" align="center">
        <script>cTab6()</script>
      </table>
    </td>
  </tr>
  <tr>
    <td><!--tab-->
      <table width="230" border="0" align="center" cellpadding="0" cellspacing="1" class="brd0" >
        <tr> 
          <td nowrap style="width:30%;line-height:120%;">
            <?php lang('plus.color_org',0); ?><INPUT id=resOrg value="" />
            <br />
            <?php lang('plus.color_code',0); ?><INPUT id=resCode value="" />
          </td>
          <td align="center" id=resDemo>
          <span style="background-color:#CCC;line-height:100%; display:inline-block;"><?php lang('plus.color_now',0); ?></span>
          </td>
          <td align="right">
          <button TYPE=SUBMIT onClick="btnSEnd('OK')"><?php lang('plus.color_setok',0); ?></button><br>
          <button onClick="btnSEnd('Cancel')"><?php lang('plus.color_cancel',0); ?></button>
          </td>
        </tr>
    </table></td>
  </tr>
</table>

<script> 
var resRGB = '#00FF00';
var resCode = jsElm.jeID('resCode');
var resDemo = jsElm.jeID('resDemo');
var pcolor = urlPara('color');
var ptitle = urlPara('title');
var parDoc = parent.document;
var resOrg = jsElm.jeID('resOrg');
resOrg.value = '#'+jsElm.pdID(pcolor).value; 
try{resOrg.style.color = resOrg.value;}catch(ex){}
//alert(getRGB+':'+pcolor);
</script>
</body>
</html>
