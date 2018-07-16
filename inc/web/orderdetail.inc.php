<?php

  global $_W,$_GPC;
  $userid = $_GPC['userid'];
  $userDetail = pdo_fetch(' SELECT name,phone FROM '.tablename('bu_user')." WHERE id={$userid}");
  $uid = pdo_fetch(' SELECT uid FROM '.tablename('mc_members')." WHERE uniacid = {$_W['uniacid']} and mobile = {$userDetail['phone']} LIMIT 1");
  $pagesize = 10;
  $pageindex = max(intval($_GPC['page']),1);
  $total = pdo_fetchcolumn(' SELECT COUNT(*) FROM '.tablename('bu_order')." WHERE uniacid = {$_W['uniacid']} and uid = {$uid['uid']} ORDER BY creattime DESC");
  $pager = pagination($total,$pageindex,$pagesize);
  $userOrder = pdo_fetchall(' SELECT * FROM '.tablename('bu_order')." WHERE uniacid = {$_W['uniacid']} and uid = {$uid['uid']} ORDER BY creattime DESC LIMIT ".($pageindex - 1) * $pagesize.','.$pagesize);
  foreach ($userOrder as $key => $value) {
    if(!empty($value['aid'])){
      $address = pdo_fetch(' SELECT username,phone FROM '.tablename('bu_address')." WHERE id={$value['aid']} LIMIT 1");
      $userinfo[$key]['name'] = $address['username'];
      $userinfo[$key]['phone'] = $address['phone'];
    }else{
      $userinfo[$key]['name'] = $userDetail['name'];
      $userinfo[$key]['phone'] = $userDetail['phone'];
    }
    $userOrder[$key]['aid'] = !empty($value['aid']) ? '用户自己兑换' : '后台兑换';
    $product = pdo_fetch(' SELECT title FROM '.tablename('hc_credit_shopping_goods')." WHERE id={$value['pid']}");
    $userOrder[$key]['pid'] = $product['title'];
    $userOrder[$key]['creattime'] = date('Y-m-d',$value['creattime']);
  }


  include $this->template('order_detail');

 ?>
