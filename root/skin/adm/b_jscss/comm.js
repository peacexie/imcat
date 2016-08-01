
function jsactMenu(menuid){
	if(!menuid){
		var a = _cbase.run.mkv.replace('.','-').split('-');
		menuid = a[0];
	}
	var e = jsElm.jeID('idf_'+menuid); 
	if(e) e.className = 'act';
}

function setEdit(disfms,hdrows,fmfix){
    if(!fmfix) fmfix = 'fm';
    var arr = new Array(disfms,hdrows);
     for(var j=0;j<arr.length;j++){
        var afm = arr[j].split(',');
        for(var i=0;i<afm.length;i++){
            var e1 = jsElm.jeID(fmfix+'['+afm[i]+']');
            var e2 = jsElm.jeID(fmfix+'_'+afm[i]+'_');
            if(j==0){
                $(e1).prop('disabled',true);
                $(e2).prop('disabled',true);
            }else{
                $(e1).parent().parent().hide();
                $(e2).parent().parent().hide();
            }
        }
     }
}

function stsetLink(e){
	var url = $(e).prop('href'); 
	//var type = $("input[name='mtype']:checked").val();
	var limit = $("input[name='limit']").val();
	var offset = $("input[name='offset']").val();
	//offset = offset.length==0 ? 0 : offset;
	url = url+'&limit='+limit+'&offset='+offset+'';
	//jsLog(type+':'+url);
	return winOpen(url,e.innerHTML);
}
