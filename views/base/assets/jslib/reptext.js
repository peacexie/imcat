
//功能：转换对象，使用递归，逐层剥到文本；
var isCurrentFt;
function transformContent(switcherId, fobj){
    if(typeof(fobj)=="object"){
        var obj=fobj.childNodes
    } else {    
        /*if(parseInt(fobj)!=0){ //在页面初始化时控制不更新当前页面语言状态；
            var switcherObj = document.getElementById(switcherId);
            with(switcherObj){
                if(parseInt(isCurrentFt)){
                    innerHTML = innerHTML.replace('简','繁')
                    title = title.replace('简','繁')
                }else{
                    innerHTML = innerHTML.replace('繁','简')
                    title = title.replace('繁','简')
                }
            }
            switcherObj.innerHTML=transformText(switcherObj.innerHTML, isCurrentFt)
            switcherObj.title=transformText(switcherObj.title, isCurrentFt)    
            
            if(isCurrentFt=="1"){isCurrentFt="0"}else{isCurrentFt="1"}
            writeCookie("isCurrentFt",isCurrentFt)
        }*/
        var obj=document.body.childNodes
    }
    for(var i=0;i<obj.length;i++){
        var OO=obj.item(i)
        if("||BR|HR|TEXTAREA|".indexOf("|"+OO.tagName+"|")>0||OO.id==switcherId)continue;
        if(OO.title!=""&&OO.title!=null)OO.title=transformText(OO.title, isCurrentFt);
        if(OO.alt!=""&&OO.alt!=null)OO.alt=transformText(OO.alt, isCurrentFt);
        if(OO.tagName=="INPUT"&&OO.value!=""&&OO.type!="text"&&OO.type!="hidden")OO.value=transformText(OO.value, isCurrentFt);
        if(OO.nodeType==3){OO.data=transformText(OO.data, isCurrentFt)}
        else transformContent(switcherId, OO)
    }
}
//功能：转换指定字符串；
function transformText(txt, isFt){
    if(txt==null || txt=="")return "";
    txt = txt.replace('锐连','卓科');
    return txt;
    //if(parseInt(isFt)){return s2t(txt)}else{return t2s(txt)}
}
transformContent(0);
