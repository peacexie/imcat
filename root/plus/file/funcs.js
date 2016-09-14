
function batSends(init){ 
	if(init){ //初始化
		jsElm.jeID("btUpload").disabled = true;
		for(i=1;i<=4;i++){ jsElm.jeID("btn"+i).disabled = true; }
		sendCnt = $("#tdfiles div").length;
	}
	if(sendNO<sendCnt){ //提交
		var d = $("div.bat_fdiv").eq(sendNO); 
		$('.bat_delbtn').eq(sendNO).prop('disabled','disabled'); 
		var id = $(d).prop('id').replace('bidiv_',''); 
		//msg & 循环
		$('#res_msg').html(lang('jfile.now',id));
		de = 13; if(batSend1(id)) { de=300; }
		setTimeout("batSends()",de);
		sendNO++;
	}else if(deelNO<sendCnt){ //结果（单项循环）
		deelNO=sendOK=0; 
		$('#tdfiles .bat_fdiv').each(function(index) {
            var t = $(this).html(); //jsLog(index+':'+t);
			//msg & 循环
			if(t.indexOf('[OK!]')>=0) { sendOK++; deelNO++; }
			if(t.indexOf('[Error]')>=0) { deelNO++; }
			$('#res_msg').html(lang('jfile.all',sendOK));
			setTimeout("batSends()",300);
        });
	}else{ //总结果
	  $('#res_msg').html(lang('jfile.all',sendOK));
	}
}

function batSend1(id){ 
    var sDoc = $(window.frames["biifr_"+id].document); //jsLog(sImg);
	var upren = $("#upren").val();
	if($("#file1",sDoc).val().length==0) { $("#bidiv_"+id).html("[Null] "+lang('jfile.null')); }
	else { $("#upren",sDoc).val(upren); $("#fup1",sDoc).submit(); sendOK++; }
	return true;
}

function batDel1(id){
	csm = $("#tdfiles div").length;
	if(csm <=1 ){
		alert(lang('jfile.file1'));	
		return;	
	}
	$('#bidiv_'+id).remove();
}

function batAdd1(n){
	addCnt++;
	var htmp = $('#ftemp').html();
	var k = '', s = '', csm = $("#tdfiles div").length;
	for(var i=0;i<n;i++){
		if(csm+i+1 > 96){
			alert(lang('jfile.filem'));	
			break;
		}
		var k = addCnt+'_'+(i+1); 
		ihtm = htmp.replace(/(0001)/g,k);
		//ihtm = htmp.replace('='+k,'='+k+'&'+$("#upren").val());//
		s += ihtm;
	}
	$('#tdfiles').append(s); 
}

function fviShow(id,url,td){
	var idImg = jsElm.jeID(id);
	if(url){
		idImg.innerHTML = lang('jfile.vpic')+':<br>'+url+'<br><img src="' + url + '" onload="imgShow(this,210,210)" border="0" />'; 
		idImg.className = "idShow";
	}else{
		idImg.innerHTML = ''; 	
		idImg.className = "idHidden";	
	}
}

function fviPath(owFile){
	var fnPos = owFile.lastIndexOf('/')+1; 
	var fsSub = owFile.substring(fnPos,owFile.length);
	jsElm.jeID('fsName').innerHTML = fsSub;	
}

function fviPick(file,type,size){
	var p=window.parent, html='', efm;
	fviPath(file); 
	try{ // For inEditor
		if(type=='image'){
			html = '<img src="'+file+'" >';
		}else{
			html = '<a href="'+file+'">'+file+'</a>';	
		}
		p.edt_Insert(fidForPick, html);
	}catch(ex){ 
	try{ // For Select/Textarea File
		efm = p.jsElm.jeID(fidForPick); 
		if(p.jsElm.jeID(fidForPick+'show')){ //jsLog(efm);
			p.mpic_madd(fidForPick,file);
		}else{
			p.jsElm.jeID(fidForPick).value = file;
			parent.layer.close(parent.layer.getFrameIndex(window.name));
		}
	}catch(ex){ 
	/*try{ // For outEditor( file ); ??? 
		window.top.opener.SetUrl(file) ;
		window.top.close() ;
		window.top.opener.focus() 
	}catch(ex){ 
	try{ // For Select Vdo
		window.opener.fviGet(file,type,size);
	}catch(ex){
		alert("无父窗口!");
		return;
	} }*/ } }
	window.close();
}

function mediaChange(){
	var vtype = otype.value;
	jsElm.jeID('ext_flag').innerHTML = lang('jfile.expar');
	jsElm.jeID('ext').value = '';
	if(vtype=='iframe'){
		jsElm.jeID('ext_flag').innerHTML = 'xx';
		jsElm.jeID('r_ext').style.display = 'none';
	}else if(vtype=='map'){
		jsElm.jeID('ext_flag').innerHTML = lang('jfile.pname');
	}else if(vtype=='bgsnd'){
		jsElm.jeID('ext_flag').innerHTML = lang('jfile.loop');
		jsElm.jeID('ext').value = 'true';
	}else{ //
		jsElm.jeID('ext_flag').innerHTML = lang('jfile.auto');
		jsElm.jeID('ext').value = 'true';
	}
	if(vtype=='map'){
		jsElm.jeID('r_map').style.display = '';
		jsElm.jeID('r_url').style.display = 'none';
	}else{
		jsElm.jeID('r_map').style.display = 'none';
		jsElm.jeID('r_url').style.display = '';
	} // ext_flag">扩展属性
}
function mediaInsert(){
	var vtype = otype.value;
	var val = type=='map' ? jsElm.jeID('map').value : jsElm.jeID('url').value;
	if(vtype==''){ 
		alert(lang('jfile.inpmtype'));
		return;
	}
	if(vtype=='map'){
	if(val.length==0){
		alert(lang('jfile.inpiont'));
		return;
	}}else{
	if(val.length==0){
		alert(lang('jfile.inpmurl'));
		return;
	}}
	var cfg = {};
	if(jsElm.jeID('pw').value.length>0) cfg.w = jsElm.jeID('pw').value;
	if(jsElm.jeID('ph').value.length>0) cfg.h = jsElm.jeID('ph').value;
	if(jsElm.jeID('ext').value.length>0) cfg.ext = jsElm.jeID('ext').value;
	window.parent.edt_InsMedia(fidForPick,vtype,val,cfg);
}

//js本地图片预览，兼容ie[6-9]、火狐、Chrome17+、Opera11+、Maxthon3  
function PreviewImage(fileObj,imgPreviewId,divPreviewId){  
    var allowExtention=".jpg,.jpeg,.gif,.png";//允许上传文件的后缀名jsElm.jeID("hfAllowPicSuffix").value;  
    var extention=fileObj.value.substring(fileObj.value.lastIndexOf(".")+1).toLowerCase();              
    var browserVersion= window.navigator.userAgent.toUpperCase();  
    if(allowExtention.indexOf(extention)>-1){   
        if(fileObj.files){//HTML5实现预览，兼容chrome、火狐7+等  
            if(window.FileReader){  
                var reader = new FileReader();   
                reader.onload = function(e){  
                    jsElm.jeID(imgPreviewId).setAttribute("src",e.target.result);  
                }    
                reader.readAsDataURL(fileObj.files[0]);  
            }else if(browserVersion.indexOf("SAFARI")>-1){  
                alert(lang('jfile.saf60'));  
            }  
        }else if (browserVersion.indexOf("MSIE")>-1){  
            if(browserVersion.indexOf("MSIE 6")>-1){//ie6  
                jsElm.jeID(imgPreviewId).setAttribute("src",fileObj.value);  
            }else{//ie[7-9]  
                fileObj.select();  
                if(browserVersion.indexOf("MSIE 9")>-1)  
                    fileObj.blur();//不加上document.selection.createRange().text在ie9会拒绝访问  
                var newPreview =jsElm.jeID(divPreviewId+"New");  
                if(newPreview==null){  
                    newPreview =document.createElement("div");  
                    newPreview.setAttribute("id",divPreviewId+"New");  
                    newPreview.style.width = jsElm.jeID(imgPreviewId).width+"px";  
                    newPreview.style.height = jsElm.jeID(imgPreviewId).height+"px";  
                    newPreview.style.border="solid 1px #d2e2e2";  
                }  
                newPreview.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod='scale',src='" + document.selection.createRange().text + "')";                              
                var tempDivPreview=jsElm.jeID(divPreviewId);  
                tempDivPreview.parentNode.insertBefore(newPreview,tempDivPreview);  
                tempDivPreview.style.display="none";                      
            }  
        }else if(browserVersion.indexOf("FIREFOX")>-1){//firefox  
            var firefoxVersion= parseFloat(browserVersion.toLowerCase().match(/firefox\/([\d.]+)/)[1]);  
            if(firefoxVersion<7){//firefox7以下版本  
                jsElm.jeID(imgPreviewId).setAttribute("src",fileObj.files[0].getAsDataURL());  
            }else{//firefox7.0+                      
                jsElm.jeID(imgPreviewId).setAttribute("src",window.URL.createObjectURL(fileObj.files[0]));  
            }  
        }else{  
            jsElm.jeID(imgPreviewId).setAttribute("src",fileObj.value);  
        }           
    }else{  
        alert(lang('jfile.fixfile',allowExtention));  
        fileObj.value="";//清空选中文件  
        if(browserVersion.indexOf("MSIE")>-1){                          
            fileObj.select();  
            document.selection.clear();  
        }                  
        fileObj.outerHTML=fileObj.outerHTML;  
    }  
}  
