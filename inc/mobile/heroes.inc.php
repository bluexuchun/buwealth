<?php
  global $_W,$_GPC;
  $inits = $this->_init();
  $hero_content = iunserializer($inits['index']['heroes']);
  $id = $_GPC['id'];
  // $userInfo = cache_load('userInfo');
  $heroes = pdo_fetchall(' SELECT * FROM '.tablename('bu_hero').' WHERE uniacid=:uniacid ORDER BY `displayorder` DESC ,`createtime` DESC ',array(':uniacid'=>$_W['uniacid']));
  // var_dump($heroes);
  include $this->template('heroes');
?>
