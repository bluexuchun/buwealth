<?php
	global $_W,$_GPC;
	$uid = $_GPC['userid'];
	$info=pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid AND id=:uid ',array(':uniacid'=>$_W['uniacid'],':uid'=>$uid));
	if (!empty($info)) {
		$phone=$info['phone'];
		$uniacid=$info['uniacid'];
		$address=pdo_fetchall(' SELECT * FROM '.tablename('bu_address').' WHERE uniacid=:uniacid and phone=:mobile ',array(':uniacid'=>$uniacid,':mobile'=>$phone));

		if (!empty($address)) {
			$useraddress=$address;
		}else {
			message('该用户还没有填写联系地址');
		}
	}else{
		message('用户不存在');
	}
	include $this->template('useraddress');
  ?>
