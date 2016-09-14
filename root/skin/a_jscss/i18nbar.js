
// 多语言翻译工具(i18n_bar)

var i18nb_apis = [
	'baidu.com(,)http://fanyi.baidu.com/transpage?query=(url)&source=url&ie=utf8&from=auto&to=(to)&render=1',
	'google.hk(,)http://translate.google.com.hk/translate?hl=zh-CN&ie=UTF8&prev=_t&sl=auto&tl=(to)&u=(url)',
	'google.tw(,)http://translate.google.com.tw/translate?hl=zh-CN&ie=UTF8&prev=_t&sl=auto&tl=(to)&u=(url)',
	'google.br(,)http://translate.google.com.br/translate?hl=zh-CN&ie=UTF8&prev=_t&sl=auto&tl=(to)&u=(url)',
	'google.au(,)http://translate.google.com.au/translate?hl=zh-CN&ie=UTF8&prev=_t&sl=auto&tl=(to)&u=(url)',
	'google.jp(,)http://translate.google.co.jp/translate?hl=zh-CN&ie=UTF8&prev=_t&sl=auto&tl=(to)&u=(url)',
	'google.uk(,)http://translate.google.co.uk/translate?hl=zh-CN&ie=UTF8&prev=_t&sl=auto&tl=(to)&u=(url)',
	'google.in(,)http://translate.google.co.in/translate?hl=zh-CN&ie=UTF8&prev=_t&sl=auto&tl=(to)&u=(url)',
	'google.fr(,)http://translate.google.fr/translate?hl=zh-CN&ie=UTF8&prev=_t&sl=auto&tl=(to)&u=(url)',
	'google.de(,)http://translate.google.de/translate?hl=zh-CN&ie=UTF8&prev=_t&sl=auto&tl=(to)&u=(url)',
	'google.ru(,)http://translate.google.ru/translate?hl=zh-CN&ie=UTF8&prev=_t&sl=auto&tl=(to)&u=(url)',
	'google.it(,)http://translate.google.it/translate?hl=zh-CN&ie=UTF8&prev=_t&sl=auto&tl=(to)&u=(url)',
	'google.ca(,)http://translate.google.ca/translate?hl=zh-CN&ie=UTF8&prev=_t&sl=auto&tl=(to)&u=(url)',
	'google.com(,)http://translate.google.com/translate?hl=zh-CN&ie=UTF8&prev=_t&sl=auto&tl=(to)&u=(url)'
];

var i18nb_langs = [
	'Arabic(,)  عربي - 阿拉伯       (,)baidu=ar,google=ar',
	'Chinese(,) 汉语 - 中文         (,)baidu=zh,google=zh-CN',
	'English(,) (UK)Britain - 英    (,)baidu=en,google=en',
	'French(,)  Français - 法       (,)baidu=fr,google=fr',
	'Russian(,) Русский - 俄 (,)baidu=ru,google=ru',
	'Spanish(,) Español - 西班牙    (,)baidu=spa,google=es',
	'German(,)  Deutsch - 德        (,)baidu=de,google=de',
	'Japanese(,)日本語 - 日         (,)baidu=jp,google=ja',
	'Korean(,)  한국어 - 韩         (,)baidu=kor,google=ko',
]; //英法俄，中阿西，德日韩

function i18nb_icfg(mod,key){
	eval("var tab = i18nb_"+mod+";");
	for(var i=0;i<tab.length;i++){
		if(tab[i].indexOf(key+'(,)')==0) return tab[i];
	}
}

function i18nb_fmturl(url){
	url = url ? url : window.location.href; //?=&
	url = url.replace('?','%3F').replace(/\&/g,'%26').replace(/\=/g,'%3D');
	return url;
}

function i18nb_ito(api,cfg){
	var a1 = api.split('.'); // google.ca 
	var a2 = cfg.split('(,)'); // 'English(,) (UK)Britain  (,)baidu=en,google=en', 
	var a3 = a2[2].split(','); 
	var to = '';
	for(var i=0;i<a3.length;i++){
		if(a3[i].indexOf(a1[0]+'=')==0){
			to = a3[i].replace(a1[0]+'=','');
			break;
		}
	}
	return to;
}

function i18nb_main(from,obj,api,url){
	obj = $('#'+obj).val();
	api = $('#'+api).val();
	if(!obj || !api) return '';
	var url = i18nb_fmturl(url);
	var tpl = i18nb_icfg('apis',api).replace(api+'(,)','');
	var to = i18nb_ito(api,i18nb_icfg('langs',obj)); 
	from = i18nb_ito(api,i18nb_icfg('langs',from));
	var re = tpl.replace('(url)',url).replace('(to)',to).replace('(from)',from);
	return re;
}

function i18nb_mui(from,obj,api,btn,cb,url){
	$('#'+btn).click(function(){
		var url = i18nb_main(from,obj,api,url);	
		cb = cb ? cb : 'i18nb_open';
		eval(cb+"('"+url+"');");
	});
	var sapis = '', slangs = '';
	for(var i=0;i<i18nb_langs.length;i++){
		var a = i18nb_langs[i].split('(,)'); if(from==a[0]) continue;
		slangs += "<option value='"+a[0]+"'>"+a[0]+" [ "+a[1].replace(/\ /g,'').replace('-',' ] ')+"</option>";
	}
	for(var i=0;i<i18nb_apis.length;i++){
		var a = i18nb_apis[i].split('(,)');
		sapis += "<option value='"+a[0]+"'>"+a[0]+"</option>";
	}
	$('#'+api).append(sapis);
	$('#'+obj).append(slangs);
}

function i18nb_open(url){
	if(url.length<12) return;
	window.open(url); //jsLog(url);
}

//http://translate.google.com.hk/translate?hl=zh-CN&ie=UTF8&prev=_t&sl=auto&tl=zh-CN&u=http://www.pswpower.com/peng/pic.asp%3FModID%3DPicS224%26TypID%3DS210052
//http://fanyi.baidu.com/transpage?query=http://www.pswpower.com/peng/pic.asp%3FModID%3DPicS224%26TypID%3DS210052&source=url&ie=utf8&from=auto&to=zh&render=1

/*
### Demo : 
<select id="i18nb_obj"><option value="">－选择语言 －</option></select>
<select id="i18nb_api"><option value="">－翻译API －</option></select>
<input id="i18nb_btn" type="button" value="翻译">
i18nb_mui('Chinese','i18nb_obj','i18nb_api','i18nb_btn'); //,'i18nb_open','http://www.xxx.com/path/test.htm'
### Note : 
*/
