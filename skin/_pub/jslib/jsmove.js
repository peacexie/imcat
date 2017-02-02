
// 鼠标拖动层(jq) =========================================================================================

function moveDiv(idBox,idBar){  
  var isClick = false; //记录鼠标是否按下
  var ClickX,ClickY; //按下鼠标时候的坐标
  var mouseX,mouseY; //移动的时候的坐标
  var nowTop,nowLeft; //移动层距离上边和左边的距离
  var cwin = winSize(); cwin.pageWidth -= 20;
  var dwin = jeSize(idBox.replace('#',''));
  $(idBar).mousedown(function(e){ //按下鼠标
     isClick = true; 
     ClickX = e.pageX;
     ClickY = e.pageY;
     nowTop = $(idBox).css("top");
     nowLeft = $(idBox).css("left");
     nowTop = parseFloat(String(nowTop).substring(0,String(nowTop).indexOf("px")));
     nowLeft = parseFloat(String(nowLeft).substring(0,String(nowLeft).indexOf("px")));
  }); //moveDiv click fun
  $(idBar).mousemove(function(e){ //移动鼠标
      mouseX = e.pageX;
      mouseY = e.pageY;
      if(isClick &&mouseX>0 &&mouseY>0){ 
          var newTop = parseFloat(mouseY-ClickY)+nowTop;
          var newLeft = parseFloat(mouseX-ClickX)+nowLeft;
          if(newTop<5) newTop = 5;
          if(newLeft<5) newLeft = 5;
          if(newLeft+dwin.width>cwin.pageWidth) newLeft = cwin.pageWidth - dwin.width;
          $(idBox).css({"top":newTop});
          $(idBox).css({"left":newLeft});
      } //if end       
  });
  $(idBar).mouseup(function(e){ //松开鼠标
      isClick = false; 
  }); 
}
// Demo 
// <div id="moveBox" style="position:absolute; width:400px; height:300px; top:20px; left:20px; border:#0C6 1px solid;">
// <div id="moveBar" style="background:#39C; cursor:move; padding:5px;">可以拖动我哦！</div>
// Content </div>
//moveDiv('#moveBox','#moveBar');

// 移动层的类/相关 =======================================================================================
function moveUnSelect(){
    try{ document.selection.empty(); }
    catch(e){ window.getSelection().removeAllRanges();}
}
function moveMain(){
    //if(!movingNow) movingNow = new moveMain();
    this.Move = function(DivID,Evt){
        if(DivID == "") return;
        var DivObj = document.getElementById(DivID);
        evt = Evt?Evt:window.event;
        if(!DivObj) return;
        var DivW = DivObj.offsetWidth;
        var DivH = DivObj.offsetHeight;
        var DivL = DivObj.offsetLeft;
        var DivT = DivObj.offsetTop;
        var TemDiv = document.createElement("div");
        TemDiv.id = DivID + "tem";
        document.body.appendChild(TemDiv);
        TemDiv.style.cssText = "width:"+DivW+"px;height:"+DivH+"px;top:"+DivT+"px;left:"+DivL+"px;position:absolute; border:#ff0000 1px dotted;z-index:500";
        this.MoveStart(DivID,evt);
    }
    this.MoveStart = function(DivID,Evt){
        var tmpMove = document.getElementById(DivID+"tem");
        if(!tmpMove) return;
        evt = Evt?Evt:window.event;
        var rLeft = evt.clientX - tmpMove.offsetLeft;
        var rTop = evt.clientY - tmpMove.offsetTop;
        document.onmousemove = function(e){
            if (!tmpMove) return;
            moveUnSelect();
            e = e ? e : window.event;
            if (e.clientX - rLeft <= 0){
                tmpMove.style.left = 0 +"px";
            }else if(e.clientX - rLeft >= document.documentElement.clientWidth - tmpMove.offsetWidth - 2){
                tmpMove.style.left = (document.documentElement.clientWidth - tmpMove.offsetWidth - 2) +"px";
            }else{
                tmpMove.style.left = e.clientX - rLeft +"px";
            }
            if (e.clientY - rTop <= 1){
                  //;
            }else{
                tmpMove.style.top = e.clientY - rTop +"px";
            }
        }
        document.onmouseup = function(){
            if (!tmpMove){return;}
            var DivObj1 = document.getElementById(DivID);
            if (!DivObj1) return;
            var l0 = tmpMove.offsetLeft;
            var t0 = tmpMove.offsetTop;
            DivObj1.style.top = t0 + "px";
            DivObj1.style.left = l0 + "px";
            try{sha_moved(DivID);}catch(sha){}
            document.body.removeChild(tmpMove);
            tmpMove = null;
        }   
    }
}
// Demo
//<div id="eout" style="position:absolute; width:400px; top: 154px; left: 270px; height:300px; border:1px solid #006">
//<div id="etitle" style="cursor:move; background:#999;" onmousedown="movingNow.Move('eout',event)">Title</div>
//This is the Demo for Move Div ... </div>\
// var movingNow = new moveMain()

/*
function mouseMove(ev) {
    ev2 = ev || window.event;
    mousePos = mouseCoords(ev2);
}
function mouseCoords(ev) {
    if (ev.pageX || ev.pageY) {
        return { x: ev.pageX, y: ev.pageY };
    }
    try{ //IE6下,在开窗中再打开设置窗,显示错误,但不影响功能。
      return {  
        x: ev.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft),
        y: ev.clientY + (document.documentElement.scrollTop || document.body.scrollTop)
      };
    }catch(e){}
}
//var mousePos; document.onmousemove = mouseMove;
*/



/*
$(function(){
    var dragging = false;
    var iX, iY;
    $("#drag").mousedown(function(e) {
        dragging = true;
        iX = e.clientX - this.offsetLeft;
        iY = e.clientY - this.offsetTop;
        this.setCapture && this.setCapture();
        return false;
    });
    document.onmousemove = function(e) {
        if (dragging) {
        var e = e || window.event;
        var oX = e.clientX - iX;
        var oY = e.clientY - iY;
        $("#drag").css({"left":oX + "px", "top":oY + "px"});
        return false;
        }
    };
    $(document).mouseup(function(e) {
        dragging = false;
        $("#drag")[0].releaseCapture();
        e.cancelBubble = true;
    })

})
*/
/*
<style type="text/css">
    #drag{width:400px;height:300px;background:url(http://upload.yxgz.cn/uploadfile/2009/0513/20090513052611873.jpg);cursor:move;position:absolute;top:100px;left:100px;border:solid 1px #ccc;}
    h2{color:#fff;background: none repeat scroll 0 0 rgba(16, 90, 31, 0.7);color:#FFFFFF;height:40px;line-height:40px;margin:0;}
</style>
*/
/*--------------拖曳效果----------------
*原理：标记拖曳状态dragging ,坐标位置iX, iY
*         mousedown:fn(){dragging = true, 记录起始坐标位置，设置鼠标捕获}
*         mouseover:fn(){判断如果dragging = true, 则当前坐标位置 - 记录起始坐标位置，绝对定位的元素获得差值}
*         mouseup:fn(){dragging = false, 释放鼠标捕获，防止冒泡}
*/
