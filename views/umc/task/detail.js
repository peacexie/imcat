
let exqaform = $('#exqarow').html(),
    exqa_touid, exqa_type, exqa_tip, exqa_cid, exqa_next;

$('.exqa-close').click(function(){
    $('.exqarow').hide(); 
});
$(".list2 .act_notes").each(function(no, elm){ // , .list2 .x-nexrow
    //if(org_mflag=='close') return false; 
    $(elm).click(function(){
        actQa(elm);
    })
});
function actQa(elm){
    $('.exqarow').hide(); // 隐藏其他的
    exqa_cid = $(elm).parent().attr('cid'); //console.log(exqa_cid);
    exqa_next = $(elm).parent().attr('exqn'); //console.log(exqa_cid);
    let p = $(elm).parent().parent(); // next
    if($(p).next().find('textarea').length>0){
        $(p).next().show();
    }else{
        $(p).after(exqaform);
    } // <!--张王五 补充, 李赵六追问, 回复 谢永顺-->
    exqa_type = $(elm).hasClass('nexrow') ? 'qa' : 'note'; 
    exqa_touid = $(elm).attr('auid'); //console.log(exqa_type +':'+ exqa_touid);
    if(exqa_type=='qa'){ // 评论
        exqa_tip = exqa_touid==uname ? '补充' : '回复';
    }else{ // note
        exqa_tip = exqa_touid==uname ? '补充' : '追问';
    }
    if(exqa_tip!=='补充'){ 
        let toName = exqa_touid in utab ? utab[exqa_touid]['name'] : '('+exqa_touid+')';
        exqa_tip += ' ' + toName;
    } 
    exqa_tip = '备注'; // 2020-11-16:统一显示`备注` //  + (exqa_next?'-to:'+$(elm).parent().find('.exqn-flag').text():'')
    $(p).find('textarea').prop('placeholder',exqa_tip);
    console.log(uname,exqa_touid);
}
function chkQa(elm){
    let msg = $(elm).parent().parent().parent().find('textarea').val();
    msg = msg.replace(/\s+/g,"");
    if(!msg){ alert('空评论不能提交!'); return; }
    let title = $('#c_title').text();
    let exqn = exqa_next ? 1 : 0;
    $.ajax({
        type: "POST", url: qaUrl,
        data: {cid:exqa_cid, to:exqa_touid, type:exqa_type, tip:exqa_tip, msg:msg, exqn:exqn, title:title},// 你的formid
        error: function(req) {
            log(req);
        },
        success: function(data) { 
            if(data['errno']){
                alert(data['errmsg']);
            }else{
                $('.exqarow').hide();
                location.reload();
            }
        }
    });
}


function chkCancel(){
    $.ajax({
        type: "POST",
        url: cancelUrl,
        data: $('#fmApply').serialize(),// 你的formid
        //async: false,
        error: function(req) {
            //$('#btnApply').show();
            //$('#btnNull').hide();
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
                    window.location.href = $('#btnReset').prop('href');
                    //$('#btnReset').trigger("click");
                    $('#toast').fadeOut(100);
                }, 2000);
            }
        }
    });
}
function cheBack1(cid, did){
    $.ajax({
        type: "POST",
        url: back1Url,
        data: {cid, did}, // 你的formid
        //async: false,
        error: function(req) {
            //$('#btnApply').show();
            //$('#btnNull').hide();
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
                    window.location.href = $('#btnReset').prop('href');
                    //$('#btnReset').trigger("click");
                    $('#toast').fadeOut(100);
                }, 2000);
            }
        }
    });
}

function chkUrge(){
    var vres = true;
    if(vres){ 
        $('#btnApply').hide();
        $('#btnNull').show();
        $('#btnReset').text('刷新');
        postUrge();
    }
    //log(vres); 
}
function postUrge(){
    $.ajax({
        type: "POST",
        url: urgeUrl,
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
<option value="apnew">新工单</option>
<option value="assign">客服派工</option>
<option value="redo">重新派工</option>
<option value="servchk">工单确认</option>
<option value="served">服务打卡</option>
<option value="done">完成</option>
<option value="paied">付款</option>
<option value="score">评分</option>
<option value="close">关闭</option> finish
<option value="susing">挂单申请</option>
<option value="suspend">已挂单</option>
<option value="attapply">配件申请</option>
<option value="attbuy">配件购买</option>
*/

function chkForm(){
    var vres = true,
        hasmsg = '(,paied,score,susing,attapply,eqfix,)', // 强制要说明理由的操作
        noself = '(,susing,attapply,assign,redo,ushift,)', // 不能为自己的操作
        mflag = $("select[name='fm[mflag]']").val();
    if(!$("select[name='fm[mflag]']").val()){
        alert('请选择`进度`');
        return;
    }
    if(!$("input[name='fm[domsg]']").val() && hasmsg.indexOf(mflag)>0){
        alert('请填写`备注说明`');
        return;
    }
    if(!$("input[name='fm[douid]']").val() && noself.indexOf(mflag)>0){
        alert('请选择`处理人`');
        return;
    }
    let v1 = $("input[name='fm[date]']").val(), v2 = $("input[name='fm[time]']").val();
    if((!v1 || !v2) && mflag=='aptime'){
        alert('`约定日期时间`不能为空');
        return;
    }
    var fee = $("input[name='fm[fee]']").val();
    if($("select[name='fm[mflag]']").val()=='done' && (!fee || fee=='0.00')){
        if(!confirm('费用为空表示不收费！')){ 
            return;
        } 
    }
    if(vres){ 
        $('#btnApply').hide();
        $('#btnNull').show();
        $('#btnReset').text('刷新'); 
        postForm();
    }
    //log(vres); 
}
function chkMuldo(){
    if(!$("input[name='fm[atuids]']").val()){
        alert('请选择`增援人`');
        return;
    }
    $('#btnApply').hide();
    $('#btnNull').show();
    $('#btnReset').text('刷新'); 
    let url = postUrl.replace('-deel','-muldo');
    postForm(url);
}
function postForm(url){
    $('#dtip1').find('.error').hide();
    $('#dtip1').find('.okey').hide();
    $('#dtip1').show();
    $('#dtip1').find('.text').html('处理中...');
    $('#dtip1').find('.wait').show();
    $.ajax({
        type: "POST",
        url: url ? url : postUrl,
        data: $('#fmApply').serialize(),// 你的formid
        //async: false,
        error: function(req) {
            $('#dtip1').find('.wait').hide();
            $('#dtip1').find('.text').html('服务器错误！');
            log(req);
        },
        success: function(data) {
            $('#dtip1').find('.wait').hide();
            if(typeof data=='string'){ data = JSON.parse(data); }
            if(data.errno){
                $('#dtip1').find('.text').html(data.errmsg);
                $('#dtip1').find('.error').show();
            }else{
                $('#dtip1').find('.text').html('已处理，请刷新');
                $('#dtip1').find('.okey').show();
            }
            opToast('dtip1', function(){
                if(!data.errno){ $('#btnReset').trigger("click"); }
            }, 2300); // window.location.href = "http://www.baidu.com"
        }
    });
}

/*
    acts
*/
function actOp(e){
    // hide-all
    $('#row_Map').hide();
    $('#row_Score').hide();
    $('#row_Pay').hide();
    $('#row_Fee').hide();
    $('#row_Time').hide();
    // pay,date,time
    eid('fm[domsg]').value = '';
    eid('fm[map]').value = '';
    eid('fm[mapMsg]').value = '';
    eid('fm[fee]').value = '';

    eid('fm[douid]').value = '';
    eid('fm[douname]').value = '';
    defUser = ''; defDept = 0;
    // 
    if($(e).val()=='aptime'){
        $('#fm_date').val($('#fm_date').attr('uval'));
        $('#fm_time').val($('#fm_time').attr('uval'));
        $('#row_Time').show();

    }else if($(e).val()=='served'){
        $('#row_Map').show();
        eid('fm[domsg]').value = '打卡';
        mapGet();
    }else if($(e).val()=='attapply'){ 
        $('#purch-dialog').show();
    }else if($(e).val()=='done'){
        $('#row_Fee').show();
    }else if($(e).val()=='score'){
        $('#row_Score').show();
        //eid('fm[domsg]').value = '评分';
    }else if($(e).val()=='paied'){
        $('#row_Pay').show();
        //eid('fm[domsg]').value = '支付';
    }else{

    }
    // setDefs
    var defTemp = wecfgs['AppCS']['defs'][$(e).val()];
    if(defTemp){
        if(/^\d+$/.test(defTemp)){ 
            defDept = parseInt(defTemp);
        }else{
            if(defTemp=='(me)'){ defTemp = wew_uid; }
            defUser = defTemp;
            eid('fm[douid]').value = defTemp;
            setName('fm[douid]','fm[douname]');
        } //log(defDept+':'+defUser);
    }
}

/*
    定位回调
*/
function mapOpen(pos){
    var tmp = pos.split(','); //lat,long
    wx.openLocation({
        latitude: parseFloat(tmp[0]), // 纬度，浮点数，范围为90 ~ -90
        longitude: parseFloat(tmp[1]), // 经度，浮点数，范围为180 ~ -180。
        name: '', // 位置名
        address: '', // 地址详情说明
        scale: 16, // 地图缩放级别,整形值,范围从1~28。默认为16
    });
}
function mapGet(){
    wx.getLocation({
        type: 'gcj02', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
        success: function (res) {
            var lat = res.latitude; // 纬度，浮点数，范围为90 ~ -90
            var long = res.longitude; // 经度，浮点数，范围为180 ~ -180。
            var speed = res.speed; // 速度，以米/每秒计
            if(!lat){
                eid('fm[map]').value = '';
                eid('fm[mapMsg]').value = '定位失败';
            }else{
                var accuracy = res.accuracy; // 位置精度
                eid('fm[map]').value = lat+','+long+','+speed+','+accuracy;
                eid('fm[mapMsg]').value = '定位成功:'+eid('fm[map]').value;
            }
        },
        fail: function(res) { 
            log(res);
            eid('fm[map]').value = '';
            eid('fm[mapMsg]').value = '定位失败';
        }
    });
}

function purchView(id){
    if(id){
        csApptmp = id;
        agentActs(purchView);
        return;
    }
    wx.invoke('thirdPartyOpenPage', {
        "oaType": "10002",// 10001,String:10001-发起审批；10002-查看审批详情。
        "templateId": "9de288d16634b9fa1c043d741b3d337f_913900360",// String
        "thirdNo": csApptmp,// String
        "extData": {}
        },
        function(res) {
            //log(res);
        }
    );
}

/*

*/

function purchForm(){
    csAppno++;
    var thirdNo = csAppre + csAppno, //log('appPurch', thirdNo); 
        subName = $('#sub_name').val();
    if(!subName || !$('#sub_type').val() || !$('#sub_num').val()){
        alert('请完善配件信息再提交！');
        return;
    }
    $.ajax({
        url: purchUrl,
        data: 'thirdNo='+thirdNo+'&subName='+subName+'',// $('#fmApply').serialize() -> a=1&b=2&c=3&d=4&e=5
        error: function(req) {
            log(req);
        },
        success: function(data) {
            if(typeof data=='string'){ data = JSON.parse(data); }
            //log(data); // pbtnsSend,pbtnsLoad
            if(data['errno']){
                log(data['errno']+data['errmsg']);
            }else{
                purchJSSDK(thirdNo);
                $('#pbtnsSend').hide();
                $('#pbtnsLoad').show();
            }
        }
    });
}

function purchJSSDK(thirdNo){
    wx.invoke('thirdPartyOpenPage', {
        "oaType": "10001",// 10001,String:10001-发起审批；10002-查看审批详情。
        "templateId": tplAppendix,// String
        "thirdNo": thirdNo,// String
        "extData": {
            'fieldList': [ // 
                {'title':'配件名称', 'type':'text', 'value':$('#sub_name').val()},
                {'title':'规格型号', 'type':'text', 'value':$('#sub_type').val()},
                {'title':'数量',     'type':'text', 'value':$('#sub_num').val()},
                {'title':'预计金额', 'type':'text', 'value':$('#sub_sum').val()},
                {'title':'预交日期', 'type':'text', 'value':$('#sub_date').val()},
                {'title':'备注',     'type':'text', 'value':$('#sub_rem').val()}, // +' thirdNo='+thirdNo
                {'title':'流程提示', 'type':'text', 'value':'第一领导审批完采购即可买东西；采购审批完表示已可领取东西，系统自动交给原工单处理人。'}
            ]
        }
    },
    function(res) {
        //log('invoke:thirdPartyOpenPage', res);
    });
}

/*
    选人回调
*/
function pickOne(tg){ 
    var nowUser = $("input[name='fm[douid]']").val();
    wx.invoke("selectEnterpriseContact", {
        "fromDepartmentId": defDept?defDept:0,// 必填，表示打开的通讯录从指定的部门开始展示，-1表示自己所在部门开始, 0表示从最上层开始
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

function printOpen(url){
    wx.invoke('openDefaultBrowser', {
        'url': url, 
    },function(res){
        if(res.err_msg != "openDefaultBrowser:ok"){
            log(res);
        }
    });
}