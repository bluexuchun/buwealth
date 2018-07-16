<?php
    global $_W,$_GPC;
    $ops = array('display','edit','delete','cdisplay','cedit','cdelete');
    $op = in_array($_GPC['op'],$ops) ? $_GPC['op'] : 'display';
    if($op == 'display'){
        // 分页
        $pagesize = 10;
        $total = pdo_fetchcolumn(' SELECT COUNT(*) FROM '.tablename('bu_product').' WHERE uniacid=:uniacid',array(':uniacid'=>$_W['uniacid']));
        $pageindex = max(intval($_GPC['page']),1);
        $products = pdo_fetchall(' SELECT * FROM '.tablename('bu_product').' WHERE uniacid=:uniacid LIMIT '.($pageindex - 1)*$pagesize.','.$pagesize,array(':uniacid'=>$_W['uniacid']));
        $pager = pagination($total,$pageindex,$pagesize);
        if(checksubmit()){
          if($_GPC['type'] == 'notice'){
            $data['notice'] = $_GPC['notice'];
            $result = pdo_update('bu_index',$data,array('id'=>$content['id']));
            if(!empty($result)){
              message('保存成功',$this->createWebUrl('product',array('op'=>'display')),'success');
            }else{
              message('保存失败',$this->createWebUrl('product',array('op'=>'display')),'error');
            }
          }
        }
        include $this->template('product');
    }else if($op == 'cdisplay'){
      // 分页
      $pagesize = 10;
      $total = pdo_fetchcolumn(' SELECT COUNT(*) FROM '.tablename('bu_cproduct').' WHERE uniacid=:uniacid',array(':uniacid'=>$_W['uniacid']));
      $pageindex = max(intval($_GPC['page']),1);
      $cproducts = pdo_fetchall(' SELECT * FROM '.tablename('bu_cproduct').' WHERE uniacid=:uniacid LIMIT '.($pageindex - 1)*$pagesize.','.$pagesize,array(':uniacid'=>$_W['uniacid']));
      $pager = pagination($total,$pageindex,$pagesize);
      include $this->template('product');
    }else if($op == 'cedit'){
      $id = intval($_GPC['id']);
      if(!empty($id)){
          $content = pdo_fetch(' SELECT * FROM '.tablename('bu_cproduct').' WHERE uniacid=:uniacid and id=:id limit 1',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
          if(empty($content)){
              message('未找到指定的导航',$this->createWebUrl('product',array('op'=>'display')),'error');
          }
      }
      if(checksubmit()){
        $data = array(
          'uniacid' => $_W['uniacid'],
          'catename' => $_GPC['catename'],
          'displayorder' => $_GPC['displayorder']
        );
        if(!empty($content)){
            $result = pdo_update('bu_cproduct',$data,array('id'=>$id));
        }else{
            $result = pdo_insert('bu_cproduct',$data);
        }
        if(!empty($result)){
            message('保存成功',$this->createWebUrl('product',array('op'=>'cdisplay')),'success');
        }else{
            message('保存失败','','error');
        }
      }
      include $this->template('product');
    }else if($op == 'cdelete'){
      $id = $_GPC['id'];
      if(!empty($id)){
          $result = pdo_delete('bu_cproduct',array('id'=>$id));
          if(!empty($result)){
              message('删除成功',$this->createWebUrl('product',array('op'=>'cdisplay')),'success');
          }else{
              message('删除失败',$this->createWebUrl('product',array('op'=>'cdisplay')),'error');
          }
      }
    }else if($op == 'edit'){
        $id = intval($_GPC['id']);
        $cates = pdo_fetchall(' SELECT * FROM '.tablename('bu_cproduct').' WHERE uniacid=:uniacid ORDER BY displayorder DESC',array(':uniacid'=>$_W['uniacid']));
        if(empty($cates)){
          message('分类为空，请先设置分类',$this->createWebUrl('product',array('op'=>'cedit')),'error');
        }
        if(!empty($id)){
            $content = pdo_fetch(' SELECT * FROM '.tablename('bu_product').' WHERE uniacid=:uniacid and id=:id limit 1',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
            if(empty($content)){
                message('未找到指定的导航',$this->createWebUrl('product',array('op'=>'display')),'error');
            }
        }
        if(checksubmit()){
            $data = array(
                'uniacid' => $_W['uniacid'],
                'productTitle' => $_GPC['productTitle'],
                'productDesc' => $_GPC['productDesc'],
                'productCate' => $_GPC['productCate'],
                'productIcon' => $_GPC['productIcon'],
                'displayorder' => $_GPC['displayorder'],
                'contentIcon' => $_GPC['contentIcon'],
                'contentTitle' => $_GPC['contentTitle'],
                'contentDesc' => $_GPC['contentDesc'],
                'contentParam' => iserializer($_GPC['contentParam']),
                'productContent' => $_GPC['productContent'],
                'productPcontent' => $_GPC['productPcontent'],
                'createtime' => time(),
                'is_show' => $_GPC['is_show']
            );
            if(!empty($content)){
                $result = pdo_update('bu_product',$data,array('id'=>$id));
            }else{
                $result = pdo_insert('bu_product',$data);
            }
            if(!empty($result)){
                message('保存成功',$this->createWebUrl('product',array('op'=>'display')),'success');
            }else{
                message('保存失败','','error');
            }
        }
        include $this->template('product');
    }else{
        $id = $_GPC['id'];
        if(!empty($id)){
            $result = pdo_delete('bu_product',array('id'=>$id));
            if(!empty($result)){
                message('删除成功',$this->createWebUrl('product',array('op'=>'display')),'success');
            }else{
                message('删除失败',$this->createWebUrl('product',array('op'=>'display')),'error');
            }
        }

    }
?>
