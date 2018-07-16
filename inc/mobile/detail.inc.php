<?php
  global $_W,$_GPC;
  $inits = $this->_init();
  $id = intval($_GPC['id']);
  $childId = intval($_GPC['childId']);
  if(!empty($childId)){
    $product = pdo_fetch(' SELECT * FROM '.tablename('bu_product').' WHERE uniacid=:uniacid and id=:id LIMIT 1',array(':uniacid'=>$_W['uniacid'],':id'=>$childId));
    $params = iunserializer($product['contentParam']);
    $length = count($params);
    $word = array();
    $ary = array();
    for ($i=0; $i < $length ; $i++) {
      $wordarray = null;
      $len = mb_strlen($params[$i]['name'],'UTF-8');
      for ($j=0; $j < $len; $j++) {
        $wordarray[] = mb_substr($params[$i]['name'],$j,1,'UTF-8');
      }
      $word[] = $wordarray;
      $ary[] = $params[$i];
    }
  }else{
    $product = "未找到该内容";
  }


  include $this->template('detail');
 ?>
