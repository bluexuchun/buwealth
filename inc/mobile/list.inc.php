<?php

  global $_W,$_GPC;
  $inits = $this->_init();
  $cid = $_GPC['cid'];
  if(!empty($cid)){
    $cateinfo = pdo_fetch(' SELECT * FROM '.tablename('hc_credit_shopping_category').' WHERE weid=:weid and id=:id LIMIT 1',array(':weid'=>$_W['uniacid'],':id'=>$cid));
    $goods = pdo_fetchall(' SELECT * FROM '.tablename('hc_credit_shopping_goods').' WHERE weid=:weid and pcate=:pcate and status=:status ORDER BY `marketprice` ASC , `createtime` DESC LIMIT 4',array(':weid'=>$_W['uniacid'],':pcate'=>$cid,':status'=>1));
    $nums = pdo_fetchcolumn(' SELECT COUNT(*) FROM '.tablename('hc_credit_shopping_goods').' WHERE weid=:weid and pcate=:pcate and status=:status',array(':weid'=>$_W['uniacid'],':pcate'=>$cid,':status'=>1));
    $nums = ceil($nums / 4);
  }
  include $this->template('list');
 ?>
