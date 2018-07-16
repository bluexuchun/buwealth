<?php
    global $_W,$_GPC;
    $ops = array('display','edit','delete','rdisplay','redit','rdelete');
    $op = in_array($_GPC['op'],$ops) ? $_GPC['op'] : 'display';
    $themeid = $_GPC['themeid'];
    if($op == 'display'){
        // 分页
        $pagesize = 10;
        $total = pdo_fetchcolumn(' SELECT COUNT(*) FROM '.tablename('bu_options').' WHERE uniacid=:uniacid',array(':uniacid'=>$_W['uniacid']));
        $pageindex = max(intval($_GPC['page']),1);
        $eng = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $options = pdo_fetchall(' SELECT * FROM '.tablename('bu_options').' WHERE uniacid=:uniacid and themeid=:themeid LIMIT '.($pageindex - 1)*$pagesize.','.$pagesize,array(':uniacid'=>$_W['uniacid'],':themeid'=>$themeid));
        $pager = pagination($total,$pageindex,$pagesize);
        include $this->template('options');
    }else if($op == 'edit'){
        $id = intval($_GPC['id']);
        if(!empty($id)){
            $content = pdo_fetch(' SELECT * FROM '.tablename('bu_options').' WHERE uniacid=:uniacid and id=:id and themeid=:themeid limit 1',array(':id'=>$id,':uniacid'=>$_W['uniacid'],':themeid'=>$themeid));
            if(empty($content)){
                message('未找到指定的导航',$this->createWebUrl('options',array('op'=>'display')),'error');
            }
        }
        if(checksubmit()){
            $data = array(
                'uniacid' => $_W['uniacid'],
                'themeid' => $themeid,
                'question' => $_GPC['question'],
                'options' => iserializer($_GPC['options']),
                'displayorder' => $_GPC['displayorder']
            );
            if(!empty($content)){
                $result = pdo_update('bu_options',$data,array('id'=>$id));
            }else{
                $result = pdo_insert('bu_options',$data);
            }
            if(!empty($result)){
                message('保存成功',$this->createWebUrl('options',array('op'=>'display','themeid'=>$themeid)),'success');
            }else{
                message('保存失败',$this->createWebUrl('options',array('op'=>'display','themeid'=>$themeid)),'error');
            }
        }
        include $this->template('options');
    }else if($op == 'rdisplay'){
      // 分页
      $pagesize = 10;
      $total = pdo_fetchcolumn(' SELECT COUNT(*) FROM '.tablename('bu_ranger').' WHERE uniacid=:uniacid',array(':uniacid'=>$_W['uniacid']));
      $pageindex = max(intval($_GPC['page']),1);
      $rangers = pdo_fetchall(' SELECT * FROM '.tablename('bu_ranger').' WHERE uniacid=:uniacid and themeid=:themeid LIMIT '.($pageindex - 1)*$pagesize.','.$pagesize,array(':uniacid'=>$_W['uniacid'],':themeid'=>$themeid));
      $pager = pagination($total,$pageindex,$pagesize);
      include $this->template('options');
    }else if($op == 'redit'){
      $id = intval($_GPC['id']);
      if(!empty($id)){
          $content = pdo_fetch(' SELECT * FROM '.tablename('bu_ranger').' WHERE uniacid=:uniacid and id=:id and themeid=:themeid limit 1',array(':id'=>$id,':uniacid'=>$_W['uniacid'],':themeid'=>$themeid));
          if(empty($content)){
              message('未找到指定的导航',$this->createWebUrl('options',array('op'=>'rdisplay')),'error');
          }
      }
      if(checksubmit()){
          $data = array(
              'uniacid' => $_W['uniacid'],
              'themeid' => $themeid,
              'result' => $_GPC['result'],
              'rang1' => $_GPC['rang1'],
              'rang2' => $_GPC['rang2'],
              'resultLevel' => $_GPC['resultLevel']
          );
          if(!empty($content)){
              $result = pdo_update('bu_ranger',$data,array('id'=>$id));
          }else{
              $result = pdo_insert('bu_ranger',$data);
          }
          if(!empty($result)){
              message('保存成功',$this->createWebUrl('options',array('op'=>'rdisplay','themeid'=>$themeid)),'success');
          }else{
              message('保存失败',$this->createWebUrl('options',array('op'=>'rdisplay','themeid'=>$themeid)),'error');
          }
      }
      include $this->template('options');
    }else if($op == 'rdelete'){
      $id = $_GPC['id'];
      if(!empty($id)){
          $result = pdo_delete('bu_ranger',array('id'=>$id));
          if(!empty($result)){
              message('删除成功',$this->createWebUrl('options',array('op'=>'rdisplay','themeid'=>$themeid)),'success');
          }else{
              message('删除失败',$this->createWebUrl('options',array('op'=>'rdisplay','themeid'=>$themeid)),'error');
          }
      }
    }else{
        $id = $_GPC['id'];
        if(!empty($id)){
            $result = pdo_delete('bu_options',array('id'=>$id));
            if(!empty($result)){
                message('删除成功',$this->createWebUrl('options',array('op'=>'display','themeid'=>$themeid)),'success');
            }else{
                message('删除失败',$this->createWebUrl('options',array('op'=>'display','themeid'=>$themeid)),'error');
            }
        }

    }
?>
