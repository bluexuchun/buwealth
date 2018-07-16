<?php
    global $_W,$_GPC;
    $ops = array('display','edit','delete');
    $op = in_array($_GPC['op'],$ops) ? $_GPC['op'] : 'display';
    $class=pdo_fetchall('SELECT * FROM'.tablename('hc_credit_shopping_category').' WHERE weid=:wid',array(':wid'=>$_W['uniacid']));
    if($op == 'display'){

        $content = pdo_fetch(' SELECT * FROM '.tablename('bu_index').' WHERE uniacid=:uniacid LIMIT 1',array(':uniacid'=>$_W['uniacid']));
        // 分页
        $pagesize = 10;
        $total = pdo_fetchcolumn(' SELECT COUNT(*) FROM '.tablename('bu_hero').' WHERE uniacid=:uniacid',array(':uniacid'=>$_W['uniacid']));
        $pageindex = max(intval($_GPC['page']),1);
        $herolists = pdo_fetchall(' SELECT * FROM '.tablename('bu_hero').' WHERE uniacid=:uniacid LIMIT '.($pageindex - 1)*$pagesize.','.$pagesize,array(':uniacid'=>$_W['uniacid']));
        $pager = pagination($total,$pageindex,$pagesize);
        if(checksubmit()){
          if($_GPC['type'] == 'heroes'){
            $data['heroes'] = iserializer($_GPC['heroes']);
            $result = pdo_update('bu_index',$data,array('id'=>$content['id']));
            if(!empty($result)){
              message('保存成功',$this->createWebUrl('heroes',array('op'=>'display')),'success');
            }else{
              message('保存失败',$this->createWebUrl('heroes',array('op'=>'display')),'error');
            }
          }
        }
        include $this->template('heroes');
    }else if($op == 'edit'){
        $id = intval($_GPC['id']);
        if(!empty($id)){
            $content = pdo_fetch(' SELECT * FROM '.tablename('bu_hero').' WHERE uniacid=:uniacid and id=:id limit 1',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
            if(empty($content)){
                message('未找到指定的导航',$this->createWebUrl('heroes',array('op'=>'display')),'error');
            }
        }
        if(checksubmit()){

            $data = array(
                'uniacid' => $_W['uniacid'],
                'heroTitle' => $_GPC['heroTitle'],
                'heroDesc' => $_GPC['heroDesc'],
                'heroIcon' => $_GPC['heroIcon'],
                'displayorder' => $_GPC['displayorder'],
                'wid'=>$_GPC['wid'],
                'createtime' => time()
            );
            if(!empty($content)){
                $result = pdo_update('bu_hero',$data,array('id'=>$id));
            }else{
                $result = pdo_insert('bu_hero',$data);
            }
            if(!empty($result)){
                message('保存成功',$this->createWebUrl('heroes',array('op'=>'display')),'success');
            }else{
                message('保存失败','','error');
            }
        }
        include $this->template('heroes');
    }else{
        $id = $_GPC['id'];
        if(!empty($id)){
            $result = pdo_delete('bu_hero',array('id'=>$id));
            if(!empty($result)){
                message('删除成功',$this->createWebUrl('heroes',array('op'=>'display')),'success');
            }else{
                message('删除失败',$this->createWebUrl('heroes',array('op'=>'display')),'error');
            }
        }

    }
?>
