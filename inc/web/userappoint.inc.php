<?php
    global $_W,$_GPC;
    $ops = array('all','detail');
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
    $userid = $_GPC['userid'];
    $userinfo = pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and id=:id LIMIT 1',array(':uniacid'=>$_W['uniacid'],':id'=>$userid));
    $name = $userinfo['name'];
    $phone = $userinfo['phone'];
    if(!empty($userid)){
      if($op == 'all'){
          $pagesize = 10;
          $total = pdo_fetchcolumn(' SELECT COUNT(*) FROM '.tablename('bu_appoint').' WHERE uniacid=:uniacid and name=:name and phone=:phone',array(':uniacid'=>$_W['uniacid'],':name'=>$name,':phone'=>$phone));
          $pageindex = max(intval($_GPC['page']),1);
          $appoints = pdo_fetchall(' SELECT * FROM '.tablename('bu_appoint').' WHERE uniacid=:uniacid and name=:name and phone=:phone LIMIT '.($pageindex - 1)*$pagesize.','.$pagesize,array(':uniacid'=>$_W['uniacid'],':name'=>$name,':phone'=>$phone));
          $pager = pagination($total,$pageindex,$pagesize);
          include $this->template('userappoint');
      }else if($op == 'detail'){
          $id = intval($_GPC['id']);
          if(!empty($id)){
            $content = pdo_fetch(' SELECT * FROM '.tablename('bu_appoint').' WHERE uniacid=:uniacid and id=:id LIMIT 1',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
            $childId = explode("&",$content['from']);
            $type = $childId[0];
            $productDetail = pdo_fetch(' SELECT * FROM '.tablename('bu_product').' WHERE uniacid=:uniacid and id=:id LIMIT 1',array(':uniacid'=>$_W['uniacid'],':id'=>$childId[1]));
            if(checksubmit()){
              $data = array(
                'status' => $_GPC['status'],
                'statustime' => time(),
                'remark' => $_GPC['remark']
              );
              $result = pdo_update('bu_appoint',$data,array('id'=>$id));
              if(!empty($result)){
                  message('更新成功',$this->createWebUrl('userappoint',array('op'=>'all')),'success');
              }else{
                  message('更新失败',$this->createWebUrl('userappoint',array('op'=>'all')),'error');
              }
            }
          }else{
            message('请选择用户',$this->createWebUrl('userappoint',array('op'=>'all')),'error');
          }
          include $this->template('userappoint');
      }
    }else{
      message('没有权限进入','','error');
    }

?>
