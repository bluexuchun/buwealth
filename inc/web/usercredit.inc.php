<?php
  global $_W,$_GPC;
  load()->model('mc');
  $id = $_GPC['userid'];
  $userinfo = pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and id=:id LIMIT 1',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
  $mobile = $userinfo['phone'];
  $member = pdo_fetch(' SELECT * FROM '.tablename('mc_members').' WHERE uniacid=:uniacid and mobile=:mobile LIMIT 1',array(':uniacid'=>$_W['uniacid'],':mobile'=>$mobile));
  $product=pdo_fetchall('SELECT * FROM ' .tablename('bu_product').'WHERE uniacid=:uniacid',array(':uniacid'=>$_W['uniacid']) );
  if(checksubmit()){
    $uid = $_GPC['uid'];
    $credit = $_GPC['credit1'];
    $money=$_GPC['money'];
    $mark = $_GPC['mark'];
    $productid=$_GPC['productid'];
    $datetime=$_GPC['date'];
    $gettype=$_GPC['gettype'];
    $content=$_GPC['content'];
    $content1=$_GPC['content1'];
    $content2=$_GPC['content2'];
    if ($gettype == 1) {
      $newmark = array(
      	'productid' => $productid,
        'num'=>$money,
        'gettype'=>$gettype,
      	'datetime'=>$datetime
      );
    }else if($gettype == 2) {
      $newmark = array(
        'gettype'=>$gettype,
        'content'=>$content
      );
    }else if($gettype == 3) {
      $newmark = array(
        'gettype'=>$gettype,
        'content'=>$content1
      );
    }else if($gettype == 4) {
      $newmark = array(
        'gettype'=>$gettype,
        'content'=>$content2
      );
    }
    if(mc_credit_update($uid, 'credit1', $credit, array($_W['uid'], iserializer($newmark)))&& mc_credit_update($uid,'credit5',$money,array($_W['uid'],iserializer($newmark)))){
      $fis=$userinfo['uid'];
      $fans=pdo_fetch('SELECT openid FROM '.tablename('mc_mapping_fans').'WHERE uniacid=:uniacid and uid=:uid',array(':uniacid'=>$_W['uniacid'],':uid'=>$fis));
      $protitle=pdo_fetch('SELECT * FROM '.tablename('bu_product').'WHERE uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$productid));
      $productTitle = $protitle['productTitle'];
      $openid=$fans['openid'];
      $ingod=$this->module['config'];

      // 获取自定义模板
      if($gettype == 1){
        $zdycontent = pdo_fetch(' SELECT * FROM '.tablename('bu_remind')." WHERE uniacid={$_W['uniacid']} and template_number='goumai' limit 1");
      }else if($gettype == 2){
        $zdycontent = pdo_fetch(' SELECT * FROM '.tablename('bu_remind')." WHERE uniacid={$_W['uniacid']} and template_number='huodong' limit 1");
      }else if($gettype == 3){
        $zdycontent = pdo_fetch(' SELECT * FROM '.tablename('bu_remind')." WHERE uniacid={$_W['uniacid']} and template_number='kehu' limit 1");
      }else if($gettype == 4){
        $zdycontent = pdo_fetch(' SELECT * FROM '.tablename('bu_remind')." WHERE uniacid={$_W['uniacid']} and template_number='qita' limit 1");
      }

      if(empty($zdycontent)){
        message('请添加自定义模板','','error');
        die();
      }
      // 自定义模板
      $text = explode('$',$zdycontent['zdytext']);
      foreach ($text as $key => $value) {
        $var[$key]['up'] = strpos($value,'{');
        $var[$key]['down'] = strpos($value,'}');
        if(strpos($value,'}')){
          $var[$key]['var'] = substr($value,$var[$key]['up']+1,$var[$key]['down']-1);
          $var[$key]['uptext'] = substr($value,0,$var[$key]['up']);
          $var[$key]['downtext'] = substr($value,$var[$key]['down']+1);
        }else{
          $var[$key]['text'] = $value;
        }
      }
      foreach ($var as $key => $value) {
        if(!empty($value['text'])){
          $finaltext = $value['text'];
        }else{
          $finaltext = $finaltext.$value['uptext'].$$value['var'].$value['downtext'];
        }
      }

      if($ingod['dxtoggle'] == 1){
        $params = array(
          [
            'name'=>'money',
            'value'=>$money
          ],
          [
            'name'=>'product',
            'value'=>$protitle['productTitle']
          ]
        );
        $result = $this->sendSmsMb($mobile,$params,'zdy',$ingod['gmmb']);
      }

      if ($openid != '' &&   $ingod['mbtoggle'] == 1) {
        if($_W['account']['level'] = ACCOUNT_SUBSCRIPTION_VERIFY){

            // $infos = "产品购买通知\n";
            // $infos.= "您{$money}元购买的产品【{$productTitle}】,已经购买成功了,获得了{$credit}积分!";
            $infos = $finaltext;
            $message = array(
              'msgtype' => 'text',
              'text' => array('content' => urlencode($infos)),
              'touser' => $openid,
            );
            $account_api = WeAccount::create();
            $status = $account_api->sendCustomNotice($message);
            if (is_error($status)) {
              message('发送失败，原因为' . $status['message']);
            }
            message('积分更新成功',$this->createWebUrl('usercredit',array('userid'=>$id)),'success');
          }
      }else{
          message('积分更新成功',$this->createWebUrl('usercredit',array('userid'=>$id)),'success');
      }
    }else{
      message('积分更新失败','','error');
    }

  }
  $pagesize = 10;
  $pageindex = max(intval($_GPC['page']),1);
  $total = pdo_fetchcolumn(' SELECT COUNT(*) FROM '.tablename('mc_credits_record').' WHERE uniacid=:uniacid and uid=:uid and credittype=:credittype',array(':uniacid'=>$_W['uniacid'],':uid'=>$member['uid'],':credittype'=>'credit1'));
  $credits = pdo_fetchall(' SELECT * FROM '.tablename('mc_credits_record').' WHERE uniacid=:uniacid and uid=:uid and credittype=:credittype ORDER BY createtime DESC LIMIT '.($pageindex - 1)*$pagesize.','.$pagesize,array(':uniacid'=>$_W['uniacid'],':uid'=>$member['uid'],':credittype'=>'credit1'));
  foreach ($credits as $key => $value) {
     $operator = pdo_fetch(' SELECT * FROM '.tablename('users').' WHERE uid=:uid',array(':uid'=>$value['operator']));
     $credits[$key]['operator'] = !empty($operator['username']) ? $operator['username'] : '用户';
     $remark=iunserializer($value['remark']);
     if($remark['gettype'] == 1){
       $credits[$key]['remark'] = '产品认购:'.$remark['title'].';日期为：'.date('Y-m-d',$value['date']);
     }else if($remark['gettype'] == 2){
       $credits[$key]['remark'] = '参与活动:'.$remark['content'].';日期为：'.date('Y-m-d',$value['date']);
     }else if($remark['gettype'] == 3){
       $credits[$key]['remark'] = '推荐客户:'.$remark['content'].';日期为：'.date('Y-m-d',$value['date']);
     }else if($remark['gettype'] == 4){
       $credits[$key]['remark'] = '其他:'.$remark['content'].';日期为：'.date('Y-m-d',$value['date']);
     }else{
       $credits[$key]['remark'] = '兑换商品:'.$remark['productname'];
     }
  }

  $pager = pagination($total,$pageindex,$pagesize);
  include $this->template('usercredit');
 ?>
