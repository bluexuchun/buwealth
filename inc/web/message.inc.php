<?php
    global $_W,$_GPC;
    $ops = array('display','edit','delete');
    $op = in_array($_GPC['op'],$ops) ? $_GPC['op'] : 'display';
    if($op == 'display'){
        // 分页
        $pagesize = 10;
        $total = pdo_fetchcolumn(' SELECT COUNT(*) FROM '.tablename('bu_message').' WHERE uniacid=:uniacid',array(':uniacid'=>$_W['uniacid']));
        $pageindex = max(intval($_GPC['page']),1);
        $msgs = pdo_fetchall(' SELECT * FROM '.tablename('bu_message').' WHERE uniacid=:uniacid LIMIT '.($pageindex - 1)*$pagesize.','.$pagesize,array(':uniacid'=>$_W['uniacid']));
        $pager = pagination($total,$pageindex,$pagesize);
        if(checksubmit()){
          if($_GPC['type'] == 'notice'){
            $data['notice'] = $_GPC['notice'];
            $result = pdo_update('bu_index',$data,array('id'=>$content['id']));
            if(!empty($result)){
              message('保存成功',$this->createWebUrl('message',array('op'=>'display')),'success');
            }else{
              message('保存失败',$this->createWebUrl('message',array('op'=>'display')),'error');
            }
          }
        }
        include $this->template('message');
    }else if($op == 'edit'){
        $id = intval($_GPC['id']);
        if(!empty($id)){
            $content = pdo_fetch(' SELECT * FROM '.tablename('bu_message').' WHERE uniacid=:uniacid and id=:id limit 1',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
            if(empty($content)){
                message('未找到指定的导航',$this->createWebUrl('message',array('op'=>'display')),'error');
            }
        }
        if(checksubmit()){
            $data = array(
                'uniacid' => $_W['uniacid'],
                'mesTitle' => $_GPC['mesTitle'],
                'mesDesc' => $_GPC['mesDesc'],
                'mesIcon' => $_GPC['mesIcon'],
                'mesContent' => $_GPC['mesContent'],
                'mesPcontent' => $_GPC['mesPcontent'],
                'displayorder' => $_GPC['displayorder'],
                'createtime' => time()
            );
            if(!empty($content)){
                $result = pdo_update('bu_message',$data,array('id'=>$id));
            }else{
                $result = pdo_insert('bu_message',$data);
            }
            if(!empty($result)){
                message('保存成功',$this->createWebUrl('message',array('op'=>'display')),'success');
            }else{
                message('保存失败','','error');
            }
        }
        include $this->template('message');
    }else{
        $id = $_GPC['id'];
        if(!empty($id)){
            $result = pdo_delete('conference_news',array('id'=>$id));
            if(!empty($result)){
                message('删除成功',$this->createWebUrl('news',array('op'=>'display')),'success');
            }else{
                message('删除失败',$this->createWebUrl('news',array('op'=>'display')),'error');
            }
        }

    }
?>
