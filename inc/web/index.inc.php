<?php
    global $_W,$_GPC;
    $ops = array('display','delete');
    $op = in_array($_GPC['op'],$ops) ? $_GPC['op'] : 'display';
    if($op == 'display'){

        $content = pdo_fetch(' SELECT * FROM '.tablename('bu_index').' WHERE uniacid=:uniacid limit 1',array(':uniacid'=>$_W['uniacid']));
        if(checksubmit()){
            $data = array(
                'uniacid' => $_W['uniacid'],
                'i_banner' =>iserializer($_GPC['i_banner']),
                'i_pbanner' =>iserializer($_GPC['i_pbanner']),
                // 'i_link' => $_GPC['i_link'],
                'is_show' => $_GPC['is_show'],
                'navs' => iserializer($_GPC['navs'])
            );
            if(!empty($content)){
                $result = pdo_update('bu_index',$data,array('id'=>$content['id']));
            }else{
                $result = pdo_insert('bu_index',$data);
            }
            if(!empty($result)){
                message('保存成功',$this->createWebUrl('index',array('op'=>'display')),'success');
            }else{
                message('保存失败','','error');
            }
        }
        include $this->template('s_index');
    }
?>
