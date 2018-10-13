

// 通过id得到web元素
function jeID(id) {
    return typeof id == 'string' ? document.getElementById(id) : id;
}

// 初始化-所有点
function sha_init(id){
    movingNow = new moveMain(); 
    mapPos = jePos('MapImages'); 
    for(var i=0;i<dots_data.length;i++){
        var iarr = dots_data[i].split(':');
        var imsg = iarr[0].split('^');
        var ipos = iarr[1].split(','); 
        sha_idot(imsg[0],imsg[1],ipos[0],ipos[1]);
    }    
}
// 增加一个点
function sha_add(id,msg,offset){
    var idot = jeID('dot_'+id);
    if(idot){
        alert('已经添加！');
        return;
    }
    sha_idot(id,msg,1+offset*2,1+offset*2);
}
// 删除一个点
function sha_del(id){
    var idot = jeID('dot_'+id);
    idot.parentNode.removeChild(idot);
    var icb = jeID('cb_'+id);
    icb.disabled = false;
    icb.checked = false;
    //清理表单项目
}
// 拖动完毕,设置表单项
function sha_moved(id){ //return;
    if(dotMoved>0){
        var ePos = jePos(id);
        jeID('posStr').innerHTML = (ePos[0]-mapPos[0])+':'+(ePos[1]-mapPos[1]);
        dotMoved = 0;
    }
}
// 显示一个点
function sha_idot(id,msg,left,top){
    var idot = document.createElement('DIV'); 
    idot.setAttribute('id','dot_'+id); 
    idot.innerHTML += msg;
    idot.className = 'sha-dot';
    idot.style.left = (mapPos[0]+parseInt(left))+'px';
    idot.style.top = (mapPos[1]+parseInt(top))+'px';
    idot.onmousedown = function(e){ dotMoved=1; movingNow.Move('dot_'+id,e); }
    idot.ondblclick = function(){ sha_del(id); }
    jeID('MapDots').appendChild(idot);
    jeID('cb_'+id).disabled = true;
    //debug::jeID('posStr').innerHTML += id+', ';
    //保存表单项目
}
