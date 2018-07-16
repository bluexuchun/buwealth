<?php
    global $_W,$_GPC;
    $ops = array('all','confrim','isConfrim','delete','detail');
    $op = in_array($_GPC['op'],$ops) ? $_GPC['op'] : 'all';
    $from = array(
      'index'=>'首页',
      'product'=>'明星产品汇',
      'leader'=>'BU领航者',
      'invest'=>'投资黄金眼',
      'message'=>'新姿势讯息',
      'station'=>'伯乐有约',
      'heroes'=>'BU群英会',
      'about'=>'关于BU'
    );
    if($op == 'all'){
        $pagesize = 10;
        $total = pdo_fetchcolumn(' SELECT COUNT(*) FROM '.tablename('bu_appoint').' WHERE uniacid=:uniacid',array(':uniacid'=>$_W['uniacid']));
        $pageindex = max(intval($_GPC['page']),1);
        $appoints = pdo_fetchall(' SELECT * FROM '.tablename('bu_appoint').' WHERE uniacid=:uniacid and type=:type LIMIT '.($pageindex - 1)*$pagesize.','.$pagesize,array(':uniacid'=>$_W['uniacid'],'type'=>'appoint'));
        foreach ($appoints as $key => $value) {
            $appoints[$key]['from'] = $from[$value['from']];
        }
        $pager = pagination($total,$pageindex,$pagesize);
        include $this->template('appoint');
    }else if($op == 'confrim'){
        $pagesize = 10;
        $total = pdo_fetchcolumn(' SELECT COUNT(*) FROM '.tablename('bu_appoint').' WHERE uniacid=:uniacid and status=:status',array(':uniacid'=>$_W['uniacid'],':status'=>0));
        $pageindex = max(intval($_GPC['page']),1);
        $appoints = pdo_fetchall(' SELECT * FROM '.tablename('bu_appoint').' WHERE uniacid=:uniacid and status=:status LIMIT '.($pageindex - 1)*$pagesize.','.$pagesize,array(':uniacid'=>$_W['uniacid'],':status'=>0));
        foreach ($appoints as $key => $value) {
            $appoints[$key]['from'] = $from[$value['from']];
        }
        $pager = pagination($total,$pageindex,$pagesize);
        include $this->template('appoint');
    }else if($op == 'isConfrim'){
        $pagesize = 10;
        $total = pdo_fetchcolumn(' SELECT COUNT(*) FROM '.tablename('bu_appoint').' WHERE uniacid=:uniacid and status=:status',array(':uniacid'=>$_W['uniacid'],':status'=>1));
        $pageindex = max(intval($_GPC['page']),1);
        $appoints = pdo_fetchall(' SELECT * FROM '.tablename('bu_appoint').' WHERE uniacid=:uniacid and status=:status LIMIT '.($pageindex - 1)*$pagesize.','.$pagesize,array(':uniacid'=>$_W['uniacid'],':status'=>1));
        foreach ($appoints as $key => $value) {
            $appoints[$key]['from'] = $from[$value['from']];
        }
        $pager = pagination($total,$pageindex,$pagesize);
        include $this->template('appoint');
    }else if($op == 'detail'){
        $id = intval($_GPC['id']);
        if(!empty($id)){
          $content = pdo_fetch(' SELECT * FROM '.tablename('bu_appoint').' WHERE uniacid=:uniacid and id=:id LIMIT 1',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
          $content['from'] = $from[$content['from']];
          if(checksubmit()){
            $data = array(
              'status' => $_GPC['status'],
              'statustime' => time(),
              'remark' => $_GPC['remark']
            );
            $result = pdo_update('bu_appoint',$data,array('id'=>$id));
            if(!empty($result)){
                message('更新成功',$this->createWebUrl('appoint',array('op'=>'all')),'success');
            }else{
                message('更新失败',$this->createWebUrl('appoint',array('op'=>'all')),'error');
            }
          }
        }else{
          message('请选择用户',$this->createWebUrl('appoint',array('op'=>'all')),'error');
        }
        include $this->template('appoint');
    }else{
        $id = $_GPC['id'];
        if(!empty($id)){
            $result = pdo_delete('bu_appoint',array('id'=>$id));
            if(!empty($result)){
                message('删除成功',$this->createWebUrl('appoint',array('op'=>'all')),'success');
            }else{
                message('删除失败',$this->createWebUrl('appoint',array('op'=>'all')),'error');
            }
        }

    }
?>
