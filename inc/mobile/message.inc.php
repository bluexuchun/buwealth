<?php
  global $_W,$_GPC;
  $inits = $this->_init();
  $id = intval($_GPC['id']);
  $msgs = pdo_fetchall(' SELECT * FROM '.tablename('bu_message').' WHERE uniacid=:uniacid ORDER BY `displayorder` DESC , `createtime` DESC ',array(':uniacid'=>$_W['uniacid']));
  include $this->template('message');
 ?>
