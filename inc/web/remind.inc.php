<?php
    // 推送消息管理
    global $_W,$_GPC;

    $op = $_GPC['op'];
    if (empty($op)) {
    	$op = 'display';
    }

    $uniacid = $_W['uniacid'];

    if ($op == 'display') {
        // 模板展示页面
    	$remind = pdo_fetchall('SELECT * FROM '.tablename('bu_remind').' WHERE uniacid=:uniacid', array(':uniacid'=>$uniacid));

    } elseif ($op == 'edit') {

        // 编辑, 添加页面
    	$id = intval($_GPC['id']);
    	if (!empty($id)) {
    		$content = pdo_fetch(' SELECT * FROM '.tablename('bu_remind').' WHERE uniacid=:uniacid and id=:id limit 1',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
        if(empty($content)){
            message('未找到指定的模板', $this->createWebUrl('remind',array('op'=>'display')),'error');
        }
    	}
    	if (checksubmit()) {
    		// 编辑, 新增保存
    		$data = array(
	            'uniacid' => $uniacid,
	            'name' => $_GPC['name'],
	            'zdytext' => $_GPC['zdytext'],
	            'template_number' => $_GPC['template_number']
	        );

	        if (empty($content)) {
	        	$result = pdo_insert('bu_remind', $data);
	        } else {
	        	$result = pdo_update('bu_remind', $data, array('uniacid'=>$uniacid, 'id'=>$id));
	        }

	        if (empty($result)) {
	        	message('保存失败, 请重试!', '', 'error');die();
	        } else {
	        	message('保存成功!', $this->createWebUrl('remind', array('op'=>'display')), 'success');die();
	        }
    	}

    } elseif ($op == 'delete') {
        // 删除处理
    	$id = intval($_GPC['id']);
    	if (!empty($id)) {
    		$result = pdo_delete('bu_remind', array('id'=>$id));
	        if(!empty($result)){
	            message('删除成功!',$this->createWebUrl('remind',array('op'=>'display')),'success');
	        }else{
	            message('删除失败!',$this->createWebUrl('remind',array('op'=>'display')),'error');
	        }
    	} else {
    		message('参数错误, 请重试!',$this->createWebUrl('remind',array('op'=>'display')),'info');
    	}
    }

    include $this->template('remind');


?>
