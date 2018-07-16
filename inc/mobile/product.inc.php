<?php
  global $_W,$_GPC;
  $inits = $this->_init();
  $id = $_GPC['id'];
  $userphone = $inits['userphone'];
  $isConfrim = pdo_fetch(' SELECT `isConfrim` FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and phone=:phone LIMIT 1',array(':uniacid'=>$_W['uniacid'],':phone'=>$userphone));
  $productCate = pdo_fetchall(' SELECT * FROM '.tablename('bu_cproduct').' WHERE uniacid=:uniacid ORDER BY displayorder DESC',array(':uniacid'=>$_W['uniacid']));
  $products = pdo_fetchall(' SELECT * FROM '.tablename('bu_product').' WHERE uniacid=:uniacid ORDER BY `displayorder` DESC , `createtime` DESC',array(':uniacid'=>$_W['uniacid']));
  foreach ($productCate as $key => $cate) {
    foreach ($products as $key1 => $value) {
      if($cate['id'] == $value['productCate']){
        $newproduct[$key][] = $value;
      }
    }
  }
  $questions = pdo_fetch(' SELECT * FROM '.tablename('bu_question').' WHERE uniacid=:uniacid and status=:status',array(':uniacid'=>$_W['uniacid'],':status'=>1));
  $eng = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
  if(!empty($questions)){
    $options = pdo_fetchall(' SELECT * FROM '.tablename('bu_options').' WHERE uniacid=:uniacid and themeid=:themeid ORDER BY displayorder ASC',array(':uniacid'=>$_W['uniacid'],':themeid'=>$questions['id']));
  }

  include $this->template('product');
?>
