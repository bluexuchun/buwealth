<?php
    global $_W,$_GPC;
    $ops = array('all','detail');
    $op = in_array($_GPC['op'],$ops) ? $_GPC['op'] : 'all';
    if($op == 'all'){
        $where = ' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid';
        $wherelimit = ' SELECT COUNT(*) FROM '.tablename('bu_user').' WHERE uniacid=:uniacid';
        $datelimitst = $_GPC['datelimitst'];
        $datelimiten = $_GPC['datelimiten'];
        $pageindex = max(intval($_GPC['page']),1);
        if(checksubmit('name_search_submit')){
          $search = $_GPC['search'];
          if(!empty($search)){
            $where = $where." and name like '%{$search}%'";
            $wherelimit = $wherelimit." and name like '%{$search}%' or phone like '%{$search}%'";
            $pageindex = 1;
          }
        }
        if(checksubmit('export_submit')){
          $datelimit = $_GPC['datelimit'];
          if(!empty($datelimit)){
            $start = strtotime($datelimit['start']);
            $end = strtotime($datelimit['end']);
            $where = $where." and `registerTime` > {$start} and `registerTime` < {$end}";
          }
          $search = $_GPC['search'];
          if(!empty($search)){
            $where = $where." and name like '%{$search}%' or phone like '%{$search}%'";
          }
          $alluser = pdo_fetchall($where,array(':uniacid'=>$_W['uniacid']));
          $type = 'normal';
          $this->Ipexcel($alluser,$type);
        }
        if(!empty($datelimitst) && !empty($datelimiten)){
          $start = strtotime($datelimitst);
          $end = strtotime($datelimiten);
          $where = $where." and `registerTime` > {$start} and `registerTime` < {$end}";
          $wherelimit = $wherelimit." and `registerTime` > {$start} and `registerTime` < {$end}";
        }
        $where = $where.' ORDER BY `registerTime` DESC LIMIT ';
        $pagesize = 10;
        $total = pdo_fetchcolumn($wherelimit,array(':uniacid'=>$_W['uniacid']));
        $users = pdo_fetchall($where.($pageindex - 1)*$pagesize.','.$pagesize,array(':uniacid'=>$_W['uniacid']));
        $pager = pagination($total,$pageindex,$pagesize);
        $alluser = pdo_fetchall($where,array(':uniacid'=>$_W['uniacid']));
        include $this->template('user1');
    }else if($op == 'detail'){
        $id = intval($_GPC['id']);
        if(!empty($id)){
          $content = pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and id=:id LIMIT 1',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
          $content['from'] = $from[$content['from']];
          if(checksubmit()){
            $data = array(
              'status' => $_GPC['status'],
              'statustime' => time()
            );
            $result = pdo_update('bu_user',$data,array('id'=>$id));
            if(!empty($result)){
                message('更新成功',$this->createWebUrl('user',array('op'=>'all')),'success');
            }else{
                message('更新失败',$this->createWebUrl('user',array('op'=>'all')),'error');
            }
          }
        }else{
          message('请选择用户',$this->createWebUrl('user',array('op'=>'all')),'error');
        }
        include $this->template('user1');
    }
 ?>
