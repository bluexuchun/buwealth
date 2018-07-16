<?php
    global $_W,$_GPC;
    $ops = array('display','delete','detail');
    $op = in_array($_GPC['op'],$ops) ? $_GPC['op'] : 'display';
    if($op == 'display'){
        $pagesize = 10;
        $total = pdo_fetchcolumn(' SELECT COUNT(*) FROM '.tablename('bu_oprecord').' WHERE uniacid=:uniacid',array(':uniacid'=>$_W['uniacid']));
        $pageindex = max(intval($_GPC['page']),1);
        $records = pdo_fetchall(' SELECT * FROM '.tablename('bu_oprecord').' WHERE uniacid=:uniacid ORDER BY createtime DESC LIMIT '.($pageindex - 1)*$pagesize.','.$pagesize,array(':uniacid'=>$_W['uniacid']));
        $pager = pagination($total,$pageindex,$pagesize);
        include $this->template('record');
    }else if($op == 'detail'){
        $id = intval($_GPC['id']);
        if(!empty($id)){
          $content = pdo_fetch(' SELECT * FROM '.tablename('bu_oprecord').' WHERE uniacid=:uniacid and id=:id LIMIT 1',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
          // 题目
          $options = pdo_fetchall(' SELECT * FROM '.tablename('bu_options').' WHERE uniacid=:uniacid and themeid=:themeid ORDER BY displayorder ASC',array(':uniacid'=>$_W['uniacid'],':themeid'=>$content['themeid']));
          $title = pdo_fetch(' SELECT * FROM '.tablename('bu_product').' WHERE uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$content['childId']));
          if(checksubmit()){
            $data = array(
              'status' => $_GPC['status'],
              'statustime' => time()
            );
            $result = pdo_update('bu_record',$data,array('id'=>$id));
            if(!empty($result)){
                message('更新成功',$this->createWebUrl('record',array('op'=>'display')),'success');
            }else{
                message('更新失败',$this->createWebUrl('record',array('op'=>'display')),'error');
            }
          }
        }else{
          message('请选择用户',$this->createWebUrl('record',array('op'=>'display')),'error');
        }
        include $this->template('record');
    }else{
        $id = $_GPC['id'];
        if(!empty($id)){
            $result = pdo_delete('bu_record',array('id'=>$id));
            if(!empty($result)){
                message('删除成功',$this->createWebUrl('record',array('op'=>'display')),'success');
            }else{
                message('删除失败',$this->createWebUrl('record',array('op'=>'display')),'error');
            }
        }

    }
?>
