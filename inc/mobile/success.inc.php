<?php
  global $_W,$_GPC;
  $inits = $this->_init();
  $orderid = $_GPC['orderid'];
  $orderinfo = pdo_fetch(' SELECT * FROM '.tablename('bu_order').' WHERE uniacid=:uniacid and id=:id LIMIT 1',array(':uniacid'=>$_W['uniacid'],':id'=>$orderid));
  $good = pdo_fetch(' SELECT * FROM '.tablename('hc_credit_shopping_goods').' WHERE weid=:uniacid and id=:id LIMIT 1',array(':uniacid'=>$_W['uniacid'],':id'=>$orderinfo['pid']));
  $address = pdo_fetch(' SELECT * FROM '.tablename('bu_address').' WHERE uniacid=:uniacid and id=:id LIMIT 1',array(':uniacid'=>$_W['uniacid'],':id'=>$orderinfo['aid']));
  $member = pdo_fetch(' SELECT * FROM '.tablename('mc_members').' WHERE uniacid=:uniacid and mobile=:mobile LIMIT 1',array(':uniacid'=>$_W['uniacid'],':mobile'=>$_GPC['userphone']));

  include $this->template('success');
 ?>
