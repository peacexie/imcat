
function showTip1(id){
  $('.panel-body').hide();
  $('.panel-body').eq(id).show();
}
function showNext(){ 
    tipno++;
    if(tipno>=$('.panel-body').length) tipno = 0;
    showTip1(tipno);
}
