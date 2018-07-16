<?php
  global $_W,$_GPC;
  $inits = $this->_init();
  $id = intval($_GPC['id']);
  $childId = empty(intval($_GPC['childId'])) ? '1':intval($_GPC['childId']);
  // $item = pdo_fetch('select * from '.tablename('bu_abouts')." where id = $childId and uniacid = $_W['uniacid']");

  $item = pdo_fetch(' SELECT * FROM '.tablename('bu_abouts')." WHERE `uniacid`= :uniacid and `id`= :id",array(':uniacid'=>$_W['uniacid'],':id'=>$childId));
  $item['banner'] = tomedia($item['banner']);
  // var_dump($item['content']);
  // $item['content'] =strip_tags($item['content']);
  $item['mobileBanner'] = tomedia($item['mobileBanner']);
  // var_dump($item['mobileContent']);
  // $item['mobileContent'] = tomedia($item['mobileContent']);

  $navs = pdo_fetchall(' SELECT * FROM '.tablename('bu_abouts')." WHERE `uniacid`= :uniacid ",array(':uniacid'=>$_W['uniacid']));
  foreach ($navs as $key => &$value) {
    $navs[$key]['header'] = tomedia($value['header']);
  }
  include $this->template('about');
 ?>
