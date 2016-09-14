
function qrActs(){
	$('nav a').bind('mouseover',function(){
		var href = $(this).prop('href'); 
		var mod = $(this).find('i').prop('id').replace('qrcode_pic',''); 
		qrurl_act(mod,1,href); //jsLog(href);
	});
	$('nav a').bind('mouseout',function(){
		var mod = $(this).find('i').prop('id').replace('qrcode_pic',''); 
		qrurl_act(mod,0);
	});
	$('.qrcode_home').bind('mouseover',function(){
		var href = $(this).attr('_url');
		qrurl_act('home',1,_burl+href); //jsLog(_burl+href);
	});
	$('.qrcode_home').bind('mouseout',function(){
		qrurl_act('home',0);
	});
}

function winReset(){
	var swin = winSize(); //jsLog(swin); 
	var sdiv = jeSize('outer'); 
	// width:960px; height:540px;
	var tmpw = swin.w-20; if(tmpw>960) tmpw = 960;
	$('#outer').width(tmpw);
	tmph = swin.h-20; if(tmph>540) tmph = 540;
	$('#outer').height(tmph);
	// toph,ftop
	var toph = (swin.h - tmph)/3; 
	$('.ptop').height(parseInt(toph));
	$('.ptop').show();
	var ftop = $('#outer').height()-35;
	$(".foot").css({"top":ftop+"px"});
	var hkid = tmpw<tmph ? 1 : 0;
	impCssText(adpCssText(hkid),'peaceStyle');
}

function adpCssText(ismoble){
	var s = new Array('','');
	s[0] = "nav { margin:10px 1px 1px 10px; text-align:left; }";
	s[0] = "nav a { margin:0px 0px 5px 5px }";
	s[0] += "p.logo, h1.title { text-align: center; display:inline-block; position:absolute; }";
	s[0] += "p.logo { left:25px; top:60px; border-radius:3px; margin:0px; }";
	s[0] += "h1.title{ left:155px; top:92px; font-size:large; line-height:160%; padding:0px; margin:0px; }";
	s[1] += "nav { margin:10px auto 10px auto; text-align:center; }";
	s[1] += "nav a { margin:5px 5px 5px 5px; }";
	s[1] += "p.logo, h1.title { left:auto; top:auto; position:relative; display:block; clear:both; margin:10px auto 10px auto; }";
	return s[ismoble];
}

function impCssText(text,cssId){
	if($('#'+cssId)){ $('#'+cssId).remove(); }
	var style = document.createElement("style"); 
	style.id = cssId;
	document.getElementsByTagName("head")[0].appendChild(style);
	if(style.styleSheet){ //for ie
		style.styleSheet.cssText = text;
	}else{ //for w3c
		style.appendChild(document.createTextNode(text)); 
	}
}
//impCssText("#div{background-color:#F30;color:#FF;}","ucss01");
