if(top.location!==self.location){top.location=self.location;}
jsElm.jeID('adf_left').innerHTML = admHtmLeft + jsElm.jeID('adf_left').innerHTML;
function admJsClick(id){ 
	var _add = jsElm.ifID('adf_main').jsElm.jeID(id+'_add'); 
	if(_add){ _add.click(); }
	else{ layer.alert('请定位到当前模型列表'); } 
}
function admSetTab(id,rst){
	var a = admNavTab.split(',');
	var b = admNavName.split(',');
	for(var i=1;i<a.length;i++){ 
		var o = jsElm.jeID('left_'+a[i]); 
		o.style.display = (id==a[i]) ? '' : 'none';
		if(id==a[i]){ 
			$('#adf_title').html(b[i]); 
			if(!rst){
				var flnk = $('#left_'+a[i]+' a:first').attr('href'); 
				$('#adf_main').prop('src',flnk); 
			}
		}
	}
}
admSetTab('m1adm',1); //setTimeout("",300); 
var admReSized = 0;
function admReSize(){
  var h = (winSize().h-27)+'px';
  jsElm.jeID('adf_main').style.height = h;
  jsElm.jeID('adf_left').style.height = h;
  jsElm.jeID('adf_right').style.height = h;
}
window.onresize = function(){ admReSize(); }
if(!admReSized) admReSize();
