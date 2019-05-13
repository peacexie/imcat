
function cflower(){
    var ajurl = _cbase.run.rsite+wurl+'&dno=flower';
    $.ajax({     
        url:ajurl, type:'get', async : false, //默认为true 异步     
        error:function(){     
            layer.tips('Error-Server', '#fcbtn', {tips: [4, '#3278BA']}); 
        },
        success:function(data){     
            if(data=='success'){
                data = '献花成功！';
                $('#fclicks').html(parseInt($('#fclicks').html())+1); 
            }
            layer.tips(data, '#fclicks', {tips: [4, '#3278BA']});
        }  
    }); 
}
function cword(){
    var ajurl = _cbase.run.rsite+wurl+'&dno=word';
    var word = $('#word').val(), mname = $('#mname').val();
    if(word.length<2 || mname.length<2){
        layer.tips('请输入留言及网名(至少2个字)', '#word', {tips: [1, '#3278BA']});
        return;
    }
    ajurl += '&word='+word+'&mname='+mname+'';
    $.ajax({     
        url:ajurl, type:'get', async : false, //默认为true 异步     
        error:function(){     
            layer.tips('Error-Server', '#submit', {tips: [4, '#3278BA']}); 
        },
        success:function(data){     
            if(data=='success'){
                data = '成功,等待审核！';
                //$('#fclicks').html(parseInt($('#fclicks').html())+1); 
            }
            layer.tips(data, '#submit', {tips: [4, '#3278BA']});
        }  
    }); 
}

$(function(){

    $('.navbar-nav li').each(function(i, e){
        $(this).click(function(){
            $('.navbar-nav li').removeClass('active');
            if($(this).addClass('active'));
        });
    });

    $('#fcbtn').click(function(){ 
        cflower();   
    });


    $('#submit').click(function(){ 
        cword();   
    });
    

});

