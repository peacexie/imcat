
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
