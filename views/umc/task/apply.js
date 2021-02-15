
function setTitle(){
    //return;
    var o = $("input[name='fm[title]']");
    if($(o).val()){ return; }
    var val = 'Title...'+stext("fm[catid]"); // stext("fm[equip]")+''+
    $(o).val(val);
}
function cntDetail(){
    var val = $("textarea[name='fm[detail]']").val(), cnt = val.length;
    if(cnt>2405){ val = val.substring(0,2400)+'...';}
    $("textarea[name='fm[detail]']").text(val).val(val);
    $('#cntDtext').text(val.length);
}

function chkForm(){
    $('#btnSubmit').trigger("click");
    var vres = fmApply.checkValidity();
    if(vres){
        var catid = stext("fm[catid]",1);
        if(catid=='c6018' && !$("input[id='fm[catstr]']").val()){
            alert('请填写工单类型');
        }else if(!$("input[name='fm[douid]']").val()){
            alert('请选执行人');
        }else{
            $('#btnApply').hide();
            $('#btnNull').show();
            $('#btnReset').text('返回');
            postForm(); 
        }
    }
    //log(vres);
}
function setCstr(){
    var catid = stext("fm[catid]",1);
    $("input[id='fm[catstr]']").hide();
    if(catid=='c6018'){
        $("input[id='fm[catstr]']").show();
    }else{
        $("input[id='fm[catstr]']").val('');
    }
}
function postForm(){
    $.ajax({
        type: "POST",
        url: postUrl,
        data: $('#fmApply').serialize(),// 你的formid
        //async: false,
        error: function(req) {
            $('#btnApply').show();
            $('#btnNull').hide();
            log(req);
        },
        success: function(data) {
            if(typeof data=='string'){ data = JSON.parse(data); }
            //console.log(data);
            if(data['errno']){
                log(data['errno']+data['errmsg']);
            }else{
                $('#btnLoad').hide();
                $('#toast').fadeIn(100);
                setTimeout(function () {
                    $('#toast').fadeOut(100);
                    //window.location.href = "http://www.baidu.com"
                    $('#btnReset').trigger("click");
                }, 2000);
            }
        }
    });
}

/*
    测试-填数据
*/
var _setTest_cnt = 0;
function setTest(){ // 用于测试填写随机表单
    _setTest_cnt++;
    if(_setTest_cnt<5){ return; }
    eid('fm[catid]').selectedIndex = Math.ceil(Math.random()*2); 
    //eid('fm[equip]').selectedIndex = Math.ceil(Math.random()*3);
    setTitle(); 
    var detail = '测试内容 请忽略！\nTest '+$("input[name='fm[title]']").val()+'：测试内容。';
    $("textarea[name='fm[detail]']").text(detail).val(detail);
    var nma1 = ['张','李','王','刘','陈','谢','欧阳','司马'];
    var nma2 = ['先生','女士','Peace','Jack','Robbin','Lisa','Lina','Rose'];
    $("input[name='fm[mname]']").val(nma1[Math.floor(Math.random()*8)]+nma2[Math.floor(Math.random()*8)]);
    /*try{
        $("[name='fm[mtel]']").val('13');
    }catch(ex){
        log(ex);
    }*/
    var tels = '';
    for(var i=0;i<4;i++){ tels += Math.floor(Math.random()*10)+''+Math.floor(Math.random()*10); } 
    $("input[name='fm[mtel]']").val('13'+Math.floor(Math.random()*10)+tels);
    var adds = ['莞城','塘厦','清溪','凤岗','东城','南城','万江','虎门'];
    $("input[name='fm[maddr]']").val('东莞'+adds[Math.floor(Math.random()*8)]+Math.floor(Math.random()*666)+'路'+Math.floor(Math.random()*888)+'号');
}

/*
    选人回调
*/
function pickOne(){
    var nowUser = $("input[name='fm[douid]']").val();
    wx.invoke("selectEnterpriseContact", {
        "fromDepartmentId": -1,// 必填，表示打开的通讯录从指定的部门开始展示，-1表示自己所在部门开始, 0表示从最上层开始
        "mode": "single", // 必填，选择模式, single=表示单选，multi=表示多选
        "type": ["user"], // 必填，选择限制类型，指定[department,user]中的一个或者多个
        "selectedUserIds": [nowUser] // 非必填，已选用户ID列表。用于多次选人时可重入，single模式下请勿填入多个id
    },function(res){
        if (res.err_msg == "selectEnterpriseContact:ok")
        {
            if(typeof res.result == 'string'){
                res.result = JSON.parse(res.result) //由于目前各个终端尚未完全兼容，需要开发者额外判断result类型以保证在各个终端的兼容性
            }
            var selectedUserList = res.result.userList; // 已选的成员列表
            for (var i = 0; i < selectedUserList.length; i++){
                var user = selectedUserList[i]; 
                eid('fm[douname]').value = user.name;
                eid('fm[douid]').value = user.id;
                //log(user);
                return;
            }
        }else{
            //log(res);
        }
    });
}
function pickList(){
    var nowTab = $("input[name='fm[atuids]']").val(),
        attTab = nowTab ? nowTab.split(',') : [];
    wx.invoke("selectEnterpriseContact", {
        "fromDepartmentId": -1,// 必填，表示打开的通讯录从指定的部门开始展示，-1表示自己所在部门开始, 0表示从最上层开始
        "mode": "multi", // 必填，选择模式, single=表示单选，multi=表示多选
        "type": ["user"], // 必填，选择限制类型，指定[department,user]中的一个或者多个
        "selectedUserIds": attTab // 非必填，已选用户ID列表。用于多次选人时可重入，single模式下请勿填入多个id
    },function(res){
        if (res.err_msg == "selectEnterpriseContact:ok")
        {
            if(typeof res.result == 'string'){
                res.result = JSON.parse(res.result) //由于目前各个终端尚未完全兼容，需要开发者额外判断result类型以保证在各个终端的兼容性
            }
            var selectedUserList = res.result.userList; // 已选的成员列表
            var idStr='', nmStr = '';
            for (var i = 0; i < selectedUserList.length; i++){
                var user = selectedUserList[i];
                nmStr += (nmStr?',':'') + user.name;
                idStr += (idStr?',':'') + user.id;
                //log(user);
                //return;
            }
            eid('fm[atnames]').value = nmStr;
            eid('fm[atuids]').value = idStr;
        }else{
            //log(res);
        }
    });
}
