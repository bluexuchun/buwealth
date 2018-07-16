<?php

    global $_W,$_GPC;
    $ops = array('display','display1','display2','display3','display4','display5');
    $op = in_array($_GPC['op'],$ops) ? $_GPC['op'] : 'display';
    if($op == 'display'){
      ////var_dump('231');
      $id = 1;
      if(!empty($id)){
          $content = pdo_fetch(' SELECT * FROM '.tablename('bu_abouts').' WHERE uniacid=:uniacid and id=:id limit 1',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
          //var_dump($content);
          if(empty($content)){
              message('未找到指定的导航','','error');
          }
      }
      if(checksubmit()){
        $data = array(
          'uniacid' => $_W['uniacid'],
          'header' => $_GPC['header'],
          'banner' => $_GPC['banner'],
          'content'=> $_GPC['content'],
          'mobileHeader' =>$_GPC['mobileHeader'],
          'mobileBanner'=>$_GPC['mobileBanner'],
          'mobileContent'=>$_GPC['mobileContent'],
          'createtime'=>time()
        );
        if(!empty($content)){
            $result = pdo_update('bu_abouts',$data,array('id'=>$id));
        }else{
          $result = pdo_update('bu_abouts',$data,array('id'=>$id));
            //$result = pdo_insert('bu_abouts',$data);
        }
        if(!empty($result)){
            message('保存成功',$this->createWebUrl('abouts',array('op'=>'display')),'success');
        }else{
            message('保存失败','','error');
        }
      }
      include $this->template('abouts');
    }else if($op == 'display1'){
      ////var_dump('231');
      $id = 2;
      if(!empty($id)){
          $content = pdo_fetch(' SELECT * FROM '.tablename('bu_abouts').' WHERE uniacid=:uniacid and id=:id limit 1',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
          //var_dump($content);
          if(empty($content)){
              message('未找到指定的导航',$this->createWebUrl('abouts',array('op'=>'display')),'error');
          }
      }
      if(checksubmit()){
        $data = array(
          'uniacid' => $_W['uniacid'],
          'header' => $_GPC['header'],
          'banner' => $_GPC['banner'],
          'content'=>$_GPC['content'],
          'mobileHeader' =>$_GPC['mobileHeader'],
          'mobileBanner'=>$_GPC['mobileBanner'],
          'mobileContent'=>$_GPC['mobileContent'],
          'createtime'=>time()
        );
        if(!empty($content)){
            $result = pdo_update('bu_abouts',$data,array('id'=>$id));
        }else{
          $result = pdo_update('bu_abouts',$data,array('id'=>$id));
            //$result = pdo_insert('bu_abouts',$data);
        }
        if(!empty($result)){
            message('保存成功',$this->createWebUrl('abouts',array('op'=>'display')),'success');
        }else{
            message('保存失败','','error');
        }
      }
      include $this->template('abouts');
    }else if($op == 'display2'){
      ////var_dump('231');
      $id = 3;
      if(!empty($id)){
          $content = pdo_fetch(' SELECT * FROM '.tablename('bu_abouts').' WHERE uniacid=:uniacid and id=:id limit 1',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
          //var_dump($content);
          if(empty($content)){
              message('未找到指定的导航',$this->createWebUrl('abouts',array('op'=>'display')),'error');
          }
      }
      if(checksubmit()){
        $data = array(
          'uniacid' => $_W['uniacid'],
          'header' => $_GPC['header'],
          'banner' => $_GPC['banner'],
          'content'=>$_GPC['content'],
          'mobileHeader' =>$_GPC['mobileHeader'],
          'mobileBanner'=>$_GPC['mobileBanner'],
          'mobileContent'=>$_GPC['mobileContent'],
          'createtime'=>time()
        );
        if(!empty($content)){
            $result = pdo_update('bu_abouts',$data,array('id'=>$id));
        }else{
          $result = pdo_update('bu_abouts',$data,array('id'=>$id));
            //$result = pdo_insert('bu_abouts',$data);
        }
        if(!empty($result)){
            message('保存成功',$this->createWebUrl('abouts',array('op'=>'display')),'success');
        }else{
            message('保存失败','','error');
        }
      }
      include $this->template('abouts');
    }else if($op == 'display3'){
      ////var_dump('231');
      $id = 4;
      if(!empty($id)){
          $content = pdo_fetch(' SELECT * FROM '.tablename('bu_abouts').' WHERE uniacid=:uniacid and id=:id limit 1',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
          //var_dump($content);
          if(empty($content)){
              message('未找到指定的导航',$this->createWebUrl('abouts',array('op'=>'display')),'error');
          }
      }
      if(checksubmit()){
        $data = array(
          'uniacid' => $_W['uniacid'],
          'header' => $_GPC['header'],
          'banner' => $_GPC['banner'],
          'content'=>$_GPC['content'],
          'mobileHeader' =>$_GPC['mobileHeader'],
          'mobileBanner'=>$_GPC['mobileBanner'],
          'mobileContent'=>$_GPC['mobileContent'],
          'createtime'=>time()
        );
        if(!empty($content)){
            $result = pdo_update('bu_abouts',$data,array('id'=>$id));
        }else{
          $result = pdo_update('bu_abouts',$data,array('id'=>$id));
            //$result = pdo_insert('bu_abouts',$data);
        }
        if(!empty($result)){
            message('保存成功',$this->createWebUrl('abouts',array('op'=>'display')),'success');
        }else{
            message('保存失败','','error');
        }
      }
      include $this->template('abouts');
    }else if($op == 'display4'){
      ////var_dump('231');
      $id = 5;
      if(!empty($id)){
          $content = pdo_fetch(' SELECT * FROM '.tablename('bu_abouts').' WHERE uniacid=:uniacid and id=:id limit 1',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
          //var_dump($content);
          if(empty($content)){
              message('未找到指定的导航',$this->createWebUrl('abouts',array('op'=>'display')),'error');
          }
      }
      if(checksubmit()){
        $data = array(
          'uniacid' => $_W['uniacid'],
          'header' => $_GPC['header'],
          'banner' => $_GPC['banner'],
          'content'=>$_GPC['content'],
          'mobileHeader' =>$_GPC['mobileHeader'],
          'mobileBanner'=>$_GPC['mobileBanner'],
          'mobileContent'=>$_GPC['mobileContent'],
          'createtime'=>time()
        );
        if(!empty($content)){
            $result = pdo_update('bu_abouts',$data,array('id'=>$id));
        }else{
          $result = pdo_update('bu_abouts',$data,array('id'=>$id));
            //$result = pdo_insert('bu_abouts',$data);
        }
        if(!empty($result)){
            message('保存成功',$this->createWebUrl('abouts',array('op'=>'display')),'success');
        }else{
            message('保存失败','','error');
        }
      }
      include $this->template('abouts');
    }else if($op == 'display5'){
      ////var_dump('231');
      $id = 6;
      if(!empty($id)){
          $content = pdo_fetch(' SELECT * FROM '.tablename('bu_abouts').' WHERE uniacid=:uniacid and id=:id limit 1',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
          //var_dump($content);
          if(empty($content)){
              message('未找到指定的导航',$this->createWebUrl('abouts',array('op'=>'display')),'error');
          }
      }
      if(checksubmit()){
        $data = array(
          'uniacid' => $_W['uniacid'],
          'header' => $_GPC['header'],
          'banner' => $_GPC['banner'],
          'content'=>$_GPC['content'],
          'mobileHeader' =>$_GPC['mobileHeader'],
          'mobileBanner'=>$_GPC['mobileBanner'],
          'mobileContent'=>$_GPC['mobileContent'],
          'createtime'=>time()
        );
        if(!empty($content)){
            $result = pdo_update('bu_abouts',$data,array('id'=>$id));
        }else{
          $result = pdo_update('bu_abouts',$data,array('id'=>$id));
            //$result = pdo_insert('bu_abouts',$data);
        }
        if(!empty($result)){
            message('保存成功',$this->createWebUrl('abouts',array('op'=>'display')),'success');
        }else{
            message('保存失败','','error');
        }
      }
      include $this->template('abouts');
    }
?>
