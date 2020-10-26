
var vmod, r_exfile, r_uatt, r_uvdo, mdtmp='ptxt', 
    evf_isExtends = true;
function vinit(){
    vmod = $("select[id='fm[vtype]']"); mdtmp = $(vmod).val();
    r_exfile = $("#fm_exfile_out").parent().parent(); 
    r_uatt = $("#fm_uatt_").parent().parent(); 
    r_uvdo = $("#fm_uvdo_").parent().parent(); 
    //setTimeout(function(){
        vobjs();
    //},230);
}
function vobjs(){
    $(r_exfile).hide();
    $(r_uatt).hide();
    $(r_uvdo).hide();
    //
    $('#fm_detail_bar').show();
    $('.ke-container').show();
    $('#fm_detail_').hide();
    //
    var mod = $(vmod).val();
    if(mod=='ptxt' || !mod){
        //
    }else if(mod=='pics'){
        $(r_exfile).show();
    }else if(mod=='vdo'){
        $(r_uvdo).show();
    }else if(mod=='down'){
        $(r_uatt).show();
    }else{ // cmd,ctxt
        $('#fm_detail_bar').hide();
        $('.ke-container').hide();
        $('#fm_detail_').show();
        //KindEditor.remove('#fm_detail_'); // 过滤空白
    }
    edtReset(mod);
}
function edtReset(mod){
    var f1 = '(ptxt|pics|vdo|down)'.indexOf(mdtmp), f2 = '(cmd|ctxt)'.indexOf(mod),
        f3 = '(ptxt|pics|vdo|down)'.indexOf(mod), f4 = '(cmd|ctxt)'.indexOf(mdtmp);
    if(f1>0 && f2>0){ 
        var bk = $('#detail_valbk');
        if($(bk).val()){
            $('#fm_detail_').val($(bk).val());
            $(bk).val('');
        }else{
            var text = edt_getText('fm[detail]');
            $('#fm_detail_').val(text);
            edt_setHtml('fm[detail]', text);
        }
    }else if(f3>0 && f4>0){ //jsLog('b:txt-htm');
        var text = $('#fm_detail_').val();
        var html = text.replace(/\r\n/g,'<br/>').replace(/\r/g,'<br/>').replace(/\n/g,'<br/>');
        edt_setHtml('fm[detail]', html);
    }
    mdtmp = mod;
}
function evf_extendsValidate(msgarr,isSubmit){ // 函数名称，固定写法
    var mod = $(vmod).val(),
        text = $('#fm_detail_').val(),
        html = edt_setHtml('fm_detail_');
    //jsLog(text); jsLog(html); jsLog(isSubmit);
    if('(cmd|ctxt)'.indexOf(mod)>0){
        $('#detail_valbk').val(text);
    }else{
        $('#detail_valbk').val(html);
    }
    return isSubmit;
}
function waitVinit(){
    var fid = 'fm_detail_';
    eval("var edtObj = editor_"+fid+";");
    //jsLog('waitVinit');
    if(edtObj){
        vinit();
        $(vmod).change(function(){
            vobjs();
        });
    }else{ // 等待加载之后才初始化,否则获取不到数据
        setTimeout(function(){
            waitVinit();
        },230);
    }
}

$(function(){
    waitVinit();
})
