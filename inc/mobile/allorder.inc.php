<?php
  global $_W,$_GPC;
  $inits = $this->_init();
  $member = pdo_fetch(' SELECT * FROM '.tablename('mc_members').' WHERE uniacid=:uniacid and mobile=:mobile',array(':uniacid'=>$_W['uniacid'],':mobile'=>$_GPC['userphone']));
  $orders = pdo_fetchall(' SELECT * FROM '.tablename('bu_order').' WHERE uniacid=:uniacid and uid=:uid',array(':uniacid'=>$_W['uniacid'],':uid'=>$member['uid']));
  $goodlist = array();
  $addlist = array();
  $cates = pdo_fetchall(' SELECT * FROM '.tablename('hc_credit_shopping_category').' WHERE weid=:uniacid',array(':uniacid'=>$_W['uniacid']));
  foreach ($cates as $key => $value) {
    $cates[$key]['name'] = mb_substr($value['name'],2,strlen($value['name']),"utf-8");
  }
  foreach ($orders as $key => $value) {
    $orders[$key]['creattime'] = date('Y-m-d H:i:s',$value['creattime']);
    $goodlist[$key] = pdo_fetch(' SELECT * FROM '.tablename('hc_credit_shopping_goods').' WHERE weid=:weid and id=:id',array(':weid'=>$_W['uniacid'],':id'=>$value['pid']));
    $addlist[$key] = pdo_fetch(' SELECT * FROM '.tablename('bu_address').' WHERE uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$value['aid']));
  }
  include $this->template('allorder');

 ?>
