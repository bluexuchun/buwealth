function validatemobile(mobile) {
    var _this = 'input[name="'+mobile+'"]';
    var mobile = $(_this).val();
    if(mobile.length==0){
      alert('请输入手机号码！');
      $(_this).focus();
      return false;
    }
    if(mobile.length!=11){
       alert('请输入有效的手机号码！');
       $(_this).focus();
       return false;
     }
     var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
     if(!myreg.test(mobile)){
       alert('请输入有效的手机号码！');
       $(_this).focus();
       return false;
     }
}
function checkEmpty(params){
  var length = params.length;
  for (var i = 0; i < length; i++) {
    var _this = 'input[name="'+params[i]+'"]';
    var text = $(_this).attr('data-name') + '不能为空';
    if($(_this).val() == '' || $(_this).val() == null || $(_this).val() == undefined){
      alert(text);
      $(_this).focus();
      return false;
    }
  }
}
function opentc(type){
  var type = type;

  if(type == 'rz'){
    $('.dark').css({
      'display':'flex'
    });
    $('body,html').css({
      'overflow-y':'hidden'
    });
    $('#rz').css({
      'display':'flex'
    })
    $('#tctitle').html('请填写身份证信息，以完成实名认证');
  }else{
    $('.dark').css({
      'display':'flex'
    });
    $('body,html').css({
      'overflow-y':'hidden'
    });
    $('#wechat').css({
      'display':'flex'
    });
    console.log($('#tctitle'))
    $('#tctitle').html('微信扫码登录');
  }
}

function openDetail(type){
  var type = type;
  if(type == 'close'){
    $('.detail').css({
      'display':'none'
    });
    $('body,html').css({
      'overflow-y':'auto'
    });
    $('.decon').hide();
  }else{
    $('.detail').css({
      'display':'flex'
    });
    $('body,html').css({
      'overflow-y':'hidden'
    });
    $('.decon').each(function(){
      if($(this).attr('data-name') == type){
        $(this).show();
      }
    })
  }
}
