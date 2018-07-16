<?php
    global $_W,$_GPC;
    $inits = $this->_init();
    $id = $_GPC['id'];
    $contents = pdo_fetchall(' SELECT * FROM '.tablename('bu_invest').' WHERE uniacid=:uniacid ORDER BY `displayorder` DESC , `createtime` DESC',array(':uniacid'=>$_W['uniacid']));
    include $this->template('invest');
?>
