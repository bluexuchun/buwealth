<?php
	global $_W,$_GPC;
	$id = $_GPC['userid'];
	$info = pdo_fetch(" SELECT * FROM ".tablename('bu_user')."WHERE id = :id and uniacid = :uniacid ",array(':id'=>$id,':uniacid'=>$_W['uniacid']));
	$uid = $info['uid'];
	if (!empty($uid)) {
		$fans=pdo_fetch("SELECT openid,nickname FROM ".tablename('mc_mapping_fans')."WHERE uid = :uid and uniacid = :uniacid ",array(':uid'=>$uid,':uniacid'=>$_W['uniacid']));
	}
	$registerTime=date("Y-m-d H:i",$info['registerTime']);
	$loginTime=date("Y-m-d H:i",$info['loginTime']);
	if(checksubmit()){
		$userid = $_GPC['id'];
		$data['isauth'] = $_GPC['isauth'];

		$result = pdo_update('bu_user',$data,array('id'=>$userid));
		if(!empty($result)){
			message('保存成功',$this->createWebUrl('user'),'success');
		}else{
			message('保存失败','','error');
		}
	}
	include $this->template('userdetail');
  ?>
