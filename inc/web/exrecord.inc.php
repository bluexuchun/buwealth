<?php
  global $_W,$_GPC;
  $op=$_GPC['op']?$_GPC['op']:'all';
  if ($op=='all') {
    $info=pdo_fetchall('select * from'.tablename('bu_export_record')." WHERE uniacid = {$_W['uniacid']}");
    foreach ($info as &$value) {
      $value['starttime']=date('Y-m-d',$value['starttime']);
      $value['createtime'] = date('Y-m-d H:i',$value['createtime']);
      $value['endtime']=date('Y-m-d',$value['endtime']);
    }
  }

  include $this->template('exrecord');
 ?>
