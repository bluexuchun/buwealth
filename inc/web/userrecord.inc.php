<?php
    global $_W,$_GPC;
    $op = $_GPC['op'];
    if($op == 'detail'){
        $id = intval($_GPC['userid']);
        $userinfo = pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and id=:id LIMIT 1',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
        $username = $userinfo['name'];
        $userphone = $userinfo['phone'];
        $content = pdo_fetch(' SELECT * FROM '.tablename('bu_oprecord').' WHERE uniacid=:uniacid and username=:username and userphone=:userphone LIMIT 1',array(':uniacid'=>$_W['uniacid'],':username'=>$username,':userphone'=>$userphone));
        if(empty($content)){
          message('暂时还没有答题记录',$this->createWebUrl('user',array('op'=>'all')),'error');
        }
        // 题目
        $options = pdo_fetchall(' SELECT * FROM '.tablename('bu_options').' WHERE uniacid=:uniacid and themeid=:themeid ORDER BY displayorder ASC',array(':uniacid'=>$_W['uniacid'],':themeid'=>$content['themeid']));
        $title = pdo_fetch(' SELECT * FROM '.tablename('bu_product').' WHERE uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$content['childId']));
        include $this->template('userrecord');
    }
?>
