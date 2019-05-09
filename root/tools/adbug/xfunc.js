
function chkIdpwd(e, no, msg){
    var tmp = e.value.replace(/[\s\u4e00-\u9fa5]/g,''); // 替换空白汉字
    if(no){ // 1:pass:6-24
        var reg = /^([\S]{6,24})$/; // 非空白都要
    }else{ // 0:id:3-15
        var reg = /^[a-zA-Z]{1}([a-zA-Z0-9_@\-\.]{2,14})$/;
    }  
    if(!reg.test(tmp) || simpass.indexOf(tmp)>0){
        tmp = orgcfgs[no];
        alert(msg+simpass);
    }
    e.value = tmp;
}

/*
    密码框屏蔽了中文输入法，除非复制粘贴...
*/
