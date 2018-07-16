<?php
  global $_W,$_GPC;

  $inits = $this->_init();
  $id = $_GPC['id'];
  $good = pdo_fetch(' SELECT * FROM '.tablename('hc_credit_shopping_goods').' WHERE weid=:weid and id=:id LIMIT 1',array(':weid'=>$_W['uniacid'],':id'=>$id));

  include $this->template('goods');

 ?>
