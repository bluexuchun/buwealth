<?php
  global $_W,$_GPC;

  $inits = $this->_init();
  $isMobile = $this->isMobile();
  $goodid = $_GPC['goodid'];
  $userid = $_GPC['user_id'];
  $uidd = pdo_fetch(' SELECT `phone` FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and id=:id LIMIT 1',array(':uniacid'=>$_W['uniacid'],':id'=>$userid));
  $address = pdo_fetchall(' SELECT * FROM '.tablename('bu_address').' WHERE uniacid=:uniacid and uid=:uid',array(':uniacid'=>$_W['uniacid'],':uid'=>$userid));
  $good = pdo_fetch(' SELECT * FROM '.tablename('hc_credit_shopping_goods').' WHERE weid=:weid and id=:id',array(':weid'=>$_W['uniacid'],':id'=>$goodid));

  $chooseid = $_GPC['chooseid'];
  if(!empty($chooseid)){
    $chooseitem = pdo_fetch(' SELECT * FROM '.tablename('bu_address').' WHERE id=:id',array(':id'=>$chooseid));
  }
  include $this->template('createorder');
 ?>
