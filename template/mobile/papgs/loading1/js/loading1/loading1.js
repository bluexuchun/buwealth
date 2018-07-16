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
})