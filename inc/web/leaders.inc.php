<?php
    global $_W,$_GPC;
    $ops = array('display','edit','delete');
    $op = in_array($_GPC['op'],$ops) ? $_GPC['op'] : 'display';
    if($op == 'display'){
      ////var_dump('231');
      $id = 4;
      if(!empty($id)){
          $content = pdo_fetch(' SELECT * FROM '.tablename('bu_leaders').' WHERE uniacid=:uniacid and id=:id limit 1',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
          //var_dump($content);
          if(empty($content)){
              message('未找到指定的导航','','error');
          }
      }
      if(checksubmit()){
        $data = array(
          'uniacid' => $_W['uniacid'],
          'header' => $_GPC['header'],
          'conntent' => $_GPC['conntent'],
          'createtime'=>time()
        );
        if(!empty($content)){
            $result = pdo_update('bu_leaders',$data,array('id'=>$id));
        }else{
          $result = pdo_update('bu_leaders',$data,array('id'=>$id));
            //$result = pdo_insert('bu_abouts',$data);
        }
        if(!empty($result)){
            message('保存成功',$this->createWebUrl('leaders',array('op'=>'display')),'success');
        }else{
            message('保存失败','','error');
        }
      }
      include $this->template('leaders');
    }
?>
