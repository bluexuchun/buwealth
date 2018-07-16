<?php
    global $_W,$_GPC;
    $ops = array('all','delete','detail');
    $op = in_array($_GPC['op'],$ops) ? $_GPC['op'] : 'all';
    if($op == 'all'){
        $pagesize = 10;
        $total = pdo_fetchcolumn(' SELECT COUNT(*) FROM '.tablename('bu_appoint').' WHERE uniacid=:uniacid',array(':uniacid'=>$_W['uniacid']));
        $pageindex = max(intval($_GPC['page']),1);
        $appoints = pdo_fetchall(' SELECT * FROM '.tablename('bu_appoint').' WHERE uniacid=:uniacid and type=:type LIMIT '.($pageindex - 1)*$pagesize.','.$pagesize,array(':uniacid'=>$_W['uniacid'],'type'=>'product'));
        $pager = pagination($total,$pageindex,$pagesize);
        include $this->template('qappoint');
    }else if($op == 'detail'){
        $id = intval($_GPC['id']);
        if(!empty($id)){
          $content = pdo_fetch(' SELECT * FROM '.tablename('bu_appoint').' WHERE uniacid=:uniacid and id=:id LIMIT 1',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
          $childId = explode("&",$content['from']);
          $productDetail = pdo_fetch(' SELECT * FROM '.tablename('bu_product').' WHERE uniacid=:uniacid and id=:id LIMIT 1',array(':uniacid'=>$_W['uniacid'],':id'=>$childId[1]));
          if(checksubmit()){
            $data = array(
              'status' => $_GPC['status'],
              'statustime' => time()
            );
            $result = pdo_update('bu_appoint',$data,array('id'=>$id));
            if(!empty($result)){
                message('更新成功',$this->createWebUrl('qappoint',array('op'=>'all')),'success');
            }else{
                message('更新失败',$this->createWebUrl('qappoint',array('op'=>'all')),'error');
            }
          }
        }else{
          message('请选择用户',$this->createWebUrl('qappoint',array('op'=>'all')),'error');
        }
        include $this->template('qappoint');
    }else{
        $id = $_GPC['id'];
        if(!empty($id)){
            $result = pdo_delete('bu_appoint',array('id'=>$id));
            if(!empty($result)){
                message('删除成功',$this->createWebUrl('qappoint',array('op'=>'all')),'success');
            }else{
                message('删除失败',$this->createWebUrl('qappoint',array('op'=>'all')),'error');
            }
        }

    }
?>
