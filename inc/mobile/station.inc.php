<?php
    global $_W,$_GPC;
    $inits = $this->_init();
    $id = $_GPC['id'];
    $contents = pdo_fetchall(' SELECT * FROM '.tablename('bu_station').' WHERE uniacid=:uniacid ORDER BY displayorder ASC',array(':uniacid'=>$_W['uniacid']));
    include $this->template('station');
?>
