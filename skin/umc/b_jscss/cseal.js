
function sealMove(e){
    var tab1 = 'cseal_tl,cseal_tr,cseal_tc,cseal_bl,cseal_br,cseal_bc', 
        tab2 = 'cseal_tr,cseal_tl,cseal_tl,cseal_br,cseal_bl,cseal_bl';
    var a1 = tab1.split(','), a2 = tab2.split(',');
    var ncls = $(e).prop('class')+''; 
    for(i=0;i<a1.length;i++){
        if((','+ncls+',').indexOf(a1[i])>0){
            ncls = ncls.replace(a1[i],a2[i]);
            break;
        }
    } 
    $(e).prop('className',ncls);
}