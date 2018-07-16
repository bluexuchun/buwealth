<?php
    global $_W,$_GPC;
    $inits = $this->_init();
    $id = empty($_GPC['id']) ? '1':$_GPC['id'];
    // var_dump($inits['isMobile']);
    // print_r($inits);
    $action = $_GPC['do'];
    if($inits['isMobile']){
      $banners = iunserializer($inits['index']['i_pbanner']);
    }else{
      $banners = iunserializer($inits['index']['i_banner']);
    }


    $links = explode(";",$inits['index']['i_link']);
    $activity = pdo_fetchall(' SELECT * FROM '.tablename('bu_activity').' WHERE uniacid=:uniacid',array(':uniacid'=>$_W['uniacid']));
    $count = count($activity);
    // $userInfo = cache_load('userInfo');
    include $this->template('index');
?>
