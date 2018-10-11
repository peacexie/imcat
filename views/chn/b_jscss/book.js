
$(function(){

    var h = $(window).height()*0.90;
    $('#text').css('height',h+'px');

    var odoc = $('#odoc').html();
    $('#html').html(marked(odoc));
    $('#text').html(odoc);


    $('nav p span').each(function(i,e){
        $(this).click(function(){
            if($(this).hasClass('act')) return;
            $('nav p span').toggleClass('act');
            $('div.out').toggle();
        });
    });

});
