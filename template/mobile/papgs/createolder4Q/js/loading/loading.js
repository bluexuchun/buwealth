$(function () {
    var just=true;
    if(just){
        $('.optionO').click(function () {
            just=false;
            if($('.leave_out_op').css('height')=='0px'){
                $('.leave_out_op').animate({height:'100px'},300,function () {
                    just=true
                });
            }else {
                $('.leave_out_op').animate({height:'0px'},300,function () {
                    just=true
                });
            }

        })
    }
    var just1=true;
    if(just1){
        $('.optionT').click(function () {
            just=false;
            if($('.city_op').css('height')=='0px'){
                $('.city_op').animate({height:'100px'},300,function () {
                    just1=true
                });
            }else {
                $('.city_op').animate({height:'0px'},300,function () {
                    just1=true
                });
            }

        })
    }
    var just2=true;
    if(just2){
        $('.areaS').click(function () {
            just=false;
            if($('.areaS_op').css('height')=='0px'){
                $('.areaS_op').animate({height:'100px'},300,function () {
                    just2=true
                });
            }else {
                $('.areaS_op').animate({height:'0px'},300,function () {
                    just2=true
                });
            }

        })
    }
    $('.pc-add').click(function(e){
            e.preventDefault();
            $('.createOlder').css({'display':'none'});
            $('.cvs').css({'display':'block'});

    })
    $('.close').click(function () {
        $('.createOlder').css({'display':'block'});
        $('.cvs').css({'display':'none'});
    })
    $('.m-add').click(function(e){
        e.preventDefault();
        $('.createOlder').css({'display':'none'});
        $('.m-cvs').css({'display':'block'});

    })
    $('.back').click(function () {
        $('.createOlder').css({'display':'block'});
        $('.m-cvs').css({'display':'none'});
    })
    
    $('.Qdefault').click(function (e) {
        e.preventDefault();
        var thisSpan=$(this).parent().parent().parent();
        $('.tar').removeClass('default');
        thisSpan.find('.tar').addClass('default');
    })
})