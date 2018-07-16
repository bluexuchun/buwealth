<?php
		global $_W,$_GPC;
		$inits = $this->_init();
		$userid = $_GPC['user_id'];
		$address = pdo_fetchall(' SELECT * FROM '.tablename('bu_address').' WHERE uniacid=:uniacid and uid=:uid',array(':uniacid'=>$_W['uniacid'],':uid'=>$userid));
	  include $this->template('collectlist');
?>
