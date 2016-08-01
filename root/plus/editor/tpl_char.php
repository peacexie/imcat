<?php 
require('tpl_cfg.php'); 
require(DIR_STATIC.'/ilibs/spchars.imp_htm'); 
?>

<script>

// 预览
function sOver(e){
	//var e=event.srcElement;
	document.getElementById("preview").innerHTML = e.innerHTML;
}
function sClick(e){
	spChar = e.innerHTML; 
	window.parent.edt_Insert('<?php echo $fid; ?>', spChar);
	//document.getElementById("SymMessage").innerHTML = '['+spChar+']已经插入! 可关闭窗口或继续插入...';
	return;
}

/////////////////////////////////////////////////////////////// 

        <?php if($pSub=='peace'){ ?>
// 选项卡点击事件
function card1Click(cardID){
	var obj;
	for (var i=1;i<=7;i++){
		obj=document.getElementById("card"+i);
		obj.className="Sym_Tab1";
	}
	obj=document.getElementById("card"+cardID);
	obj.className="Sym_Tab2";

	for (var i=1;i<=7;i++){
		obj=document.getElementById("Sym_Content"+i);
		obj.style.display="none";
		obj.style.visibility='hidden';
	}
	obj=document.getElementById("Sym_Content"+cardID);
	obj.style.display="";
	obj.style.visibility='visible';
}
// onMouseOver="sOver()" onClick="sClick()"
//setEvent("onmouseover","muOver","menu01Tags","td");
for(var i=1;i<=7;i++){
  setEvent("onmouseover","sOver","Sym_Content"+i,"td");
  setEvent("onclick","sClick","Sym_Content"+i,"td");
}
card1Click(5);
  
/////////////////////////////////////////////////////////////// 
        <?php } ?>
        <?php if($pSub=='eweb'){ ?>
// Verdana,Webdings,Wingdings,Symbol,Unicode
// 选项卡点击事件 <span style="FONT-FAMILY: Verdana">;</span>
function card2Click(cardID,xStyle){
  	var oItems = document.getElementById('Sym_eWeb').getElementsByTagName('td');
	for(var i = 0;i<oItems.length;i++)
	{
	  oItems[i].innerHTML = "<span style='FONT-FAMILY: "+xStyle+"'>"+oItems[i].innerText+"</span>";
	}
	var obj;
	for (var i=11;i<=15;i++){
		obj=document.getElementById("card"+i);
		obj.className="Sym_Tab1";
	}
	obj=document.getElementById("card"+cardID);
	obj.className="Sym_Tab2";
}
setEvent("onmouseover","sOver","Sym_eWeb","td");
setEvent("onclick","sClick","Sym_eWeb","td");
card2Click(12,'Webdings');

/////////////////////////////////////////////////////////////// 
        <?php } ?>
        <?php if($pSub=='baidu'){ ?>
// 选项卡点击事件
function card3Click(cardID){
	var obj;
	for (var i=31;i<=38;i++){
		obj=document.getElementById("card"+i);
		obj.className="Sym_Tab1";
	}
	obj=document.getElementById("card"+cardID);
	obj.className="Sym_Tab2";

	for (var i=31;i<=38;i++){
		obj=document.getElementById("Sym_Content"+i);
		obj.style.display="none";
		obj.style.visibility='hidden';
	}
	obj=document.getElementById("Sym_Content"+cardID);
	obj.style.display="";
	obj.style.visibility='visible';
}

// onMouseOver="sOver()" onClick="sClick()"
//setEvent("onmouseover","muOver","menu01Tags","td");
for(var i=31;i<=38;i++){
  setEvent("onmouseover","sOver","Sym_Content"+i,"td");
  setEvent("onclick","sClick","Sym_Content"+i,"td");
}
card3Click(31);
        <?php } ?>

</script> 
</body>
</html>