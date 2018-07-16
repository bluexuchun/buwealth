<?php
    global $_W,$_GPC;
    $inits = $this->_init();
    $user_id = $_GPC['user_id'];
    $is_weixin = $this->is_weixin();
    $bind = $_GPC['type'];
    $uid = pdo_fetch(' SELECT * FROM '.tablename('mc_members').' WHERE uniacid=:uniacid and mobile=:mobile LIMIT 1',array(':uniacid'=>$_W['uniacid'],':mobile'=>$inits['userphone']));
    $credits = pdo_fetchall(' SELECT * FROM '.tablename('mc_credits_record').' WHERE uniacid=:uniacid and uid=:uid and credittype=:credittype',array(':uniacid'=>$_W['uniacid'],':uid'=>$uid['uid'],':credittype'=>'credit1'));
    // 积分获取
    $allNum = 0;
    $useNum = 0;
    $timeY = date('Y',time());
    $timem = date('m',time());
    $timed = date('d',time());
    $allCredit = 0;
    $typelists = ['产品认购','活动参与奖励','推荐客户奖励','其他'];
    foreach ($credits as $key => $value) {
      if($value['num'] > 0){
        $valist = iunserializer($value['remark']);
        if($valist['gettype'] == 1){
          $creditProduct[$key]['product'] = pdo_fetch(' SELECT * FROM '.tablename('bu_product').' WHERE uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$valist['productid']))['productTitle'];
          $creditProduct[$key]['type'] = '募集中';
          $creditProduct[$key]['date'] = $valist['datetime'].'年';
          $creditProduct[$key]['money'] = $valist['num'];
          $creditProduct[$key]['num'] = $value['num'];
          $creditProduct[$key]['createtime'] = date('Y-m-d',$value['createtime']);
          $allNum = $allNum + $valist['num'];
        }

      }else{
        $useNum = $useNum + $value['num'];
        $valist = iunserializer($value['remark']);
        $useCredit[$key]['money'] =  $value['num'];
        $useCredit[$key]['createtime'] = date('Y-m-d',$value['createtime']);
        $useCredit[$key]['goodname'] = $valist['productname'];
        $useCredit[$key]['status'] = '已完成';
      }
    }
    foreach ($credits as $key => $value) {
      if($value['num'] > 0){
        $valist = iunserializer($value['remark']);

        if($valist['gettype'] == 1){
          $getCredit[$key]['product'] = pdo_fetch(' SELECT * FROM '.tablename('bu_product').' WHERE uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$valist['productid']))['productTitle'];
          $getCredit[$key]['type'] = $typelists[$valist['gettype']-1];
          $getCredit[$key]['createtime'] = date('Y-m-d',$value['createtime']);
          $getCredit[$key]['num'] = $value['num'];
        }else{
          $getCredit[$key]['product'] = !empty($valist['content']) ? $valist['content'] : '系统赠送';
          $getCredit[$key]['type'] = !empty($valist['gettype']) ? $typelists[$valist['gettype']-1] : '其他';
          $getCredit[$key]['createtime'] = date('Y-m-d',$value['createtime']);
          $getCredit[$key]['num'] = $value['num'];
        }


        $allCredit = $allCredit + $value['num'];
      }
    }
    // var_dump($getCredit);
    if(!empty($user_id)){
      $userInfo = pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$user_id));
      $userQ = pdo_fetch(' SELECT * FROM '.tablename('bu_oprecord').' WHERE uniacid=:uniacid and username=:username and userphone=:userphone LIMIT 1',array(':uniacid'=>$_W['uniacid'],':username'=>$userInfo['name'],':userphone'=>$userInfo['phone']));
      $userQ['createtime'] = date('Y-m-d',$userQ['createtime']);
      $ranger = pdo_fetchall(' SELECT * FROM '.tablename('bu_ranger').' WHERE uniacid=:uniacid',array(':uniacid'=>$_W['uniacid']));
      foreach ($ranger as $key => $value) {
        if($value['rang1']<$userQ['result'] && $userQ['result']<$value['rang2']){
          $result = $value['result'];
        }
      }
      $userInfo['loginTime'] = date('Y-m-d',$userInfo['loginTime']);
      if($userInfo['isbind'] == 1){
        $userNickname = pdo_fetch(' SELECT * FROM '.tablename('mc_members').' WHERE uniacid=:uniacid and uid=:uid',array(':uniacid'=>$_W['uniacid'],':uid'=>$userInfo['uid']));
        $userNickname['avatar'] = substr($userNickname['avatar'],0,strlen($userNickname['avatar'])-3);
      }
    }
    include $this->template('personal');
?>
