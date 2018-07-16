<?php
/**
 * Bu财富模块微站定义
 *
 * @author bluexuchun
 * @url
 */
defined('IN_IA') or exit('Access Denied');
define('INC_PATH',ROOT_PATH.'inc/');
require_once IA_ROOT . '/addons/bu_wealth/Sms.php';
require_once IA_ROOT . '/addons/bu_wealth/PHPExcel-1.8/Classes/PHPExcel.php';
require_once IA_ROOT . '/addons/bu_wealth/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php';
class Bu_wealthModuleSite extends WeModuleSite {
    // 初始化
    public function _init(){
        global $_W,$_GPC;
        $init['setting'] = $this->module['config'];
        $init['index'] = pdo_fetch(' SELECT * FROM '.tablename('bu_index').' WHERE uniacid=:uniacid limit 1',array(':uniacid'=>$_W['uniacid']));
        $init['navs'] = array();
        $init['do'] = $_GPC['do'];
        $init['isMobile'] = $this->isMobile();
        $init['childId'] = $_GPC['childId'];
        $navs = iunserializer($init['index']['navs']);
        $init['username'] = $_GPC['username'];
        $init['userphone'] = $_GPC['userphone'];
        $inti['user_id'] = $_GPC['user_id'];
        $init['is_login'] = !empty($_GPC['is_login']) ? $_GPC['is_login'] : 0;
        $init['call'] = mb_substr($_GPC['username'],0,1,"UTF-8").'..';
        $aboutnavs = array(
          '1'=>array(
            'id'=>1,
            'title'=>'BU简介',
            'icon'=>'nav1.png'
          ),
          '2'=>array(
            'id'=>2,
            'title'=>'财富梦想',
            'icon'=>'nav2.png'
          ),
          '3'=>array(
            'id'=>3,
            'title'=>'价值原力',
            'icon'=>'nav3.png'
          ),
          '4'=>array(
            'id'=>4,
            'title'=>'荣耀心程',
            'icon'=>'nav4.png'
          ),
          '5'=>array(
            'id'=>5,
            'title'=>'核心动力',
            'icon'=>'nav5.png'
          ),
          '6'=>array(
            'id'=>6,
            'title'=>'联系我们',
            'icon'=>'nav6.png'
          )
        );
        $init['aboutnav'] = $aboutnavs;
        foreach ($navs as $key => $value) {
          $init['navs'][$value['displayorder']] = $value;
        }

        ksort($init['navs']);
        return $init;
    }
    // cancel
    public function doMobileCancel(){
      global $_W,$_GPC;
      if(isetcookie('is_login','') && isetcookie('userphone','') && isetcookie('username','') && isetcookie('user_id','')){
        $message = '清楚缓存成功';
        $success = 1;
      }else{
        $message = '清楚缓存失败';
        $success = 0;
      }
      $data['message'] = $message;
      $data['success'] = $success;
      echo json_encode($data);
    }
    // 判断用户是否已经答题
    public function doMobileIsquestion(){
      global $_W,$_GPC;
      $phone = $_GPC['phone'];
      if(!empty($phone)){
        $userinfo = pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and phone=:phone LIMIT 1',array(':uniacid'=>$_W['uniacid'],':phone'=>$phone));
        if(!empty($userinfo)){
          $data = array(
            'success' => 1,
            'message' => $userinfo['isQuestion'],
          );
          echo json_encode($data);
        }else{
          $data = array(
            'success' => 0,
            'message' => '获取信息错误',
          );
          echo json_encode($data);
        }
      }else{
        $data = array(
          'success' => 0,
          'message' => '不存在该用户',
        );
        echo json_encode($data);
      }
    }
    // 判断用户是否已经答题
    public function doMobileIsconfrim(){
      global $_W,$_GPC;
      $phone = $_GPC['phone'];
      if(!empty($phone)){
        $userinfo = pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and phone=:phone LIMIT 1',array(':uniacid'=>$_W['uniacid'],':phone'=>$phone));
        if(!empty($userinfo)){
          $updata = array(
            'isConfrim' => 1
          );
          $result = pdo_update('bu_user',$updata,array('id'=>$userinfo['id']));
          if(!empty($result)){
            $data = array(
              'status' => 1,
              'message' => '更新成功',
            );
          }else{
            $data = array(
              'status' => 0,
              'message' => '更新失败',
            );
          }
          echo json_encode($data);
        }else{
          $data = array(
            'status' => 0,
            'message' => '获取信息错误',
          );
          echo json_encode($data);
        }
      }else{
        $data = array(
          'status' => 0,
          'message' => '不存在该用户',
        );
        echo json_encode($data);
      }
    }
    // LOGIN
    public function doMobileLogin(){
        global $_W,$_GPC;
        $username = $_GPC['username'];
        $userphone = $_GPC['userphone'];
        $userpassword = $_GPC['userpassword'];
        if(!empty($username) || !empty($userphone) || !empty($userpassword)){
          $userinfo = pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and name=:name and phone=:phone',array(':uniacid'=>$_W['uniacid'],':name'=>$username,':phone'=>$userphone));
          if(!empty($userinfo)){
            $ps = authcode($userinfo['password'],'DECODE');
            if($ps == $userpassword){
              $logindata['loginTime'] = time();
              pdo_update('bu_user',$logindata,array('id'=>$userinfo['id']));
              $data = array(
                'message' => '登陆成功',
                'success' => 1,
                'name' => $userinfo['name'],
                'phone' => $userinfo['phone']
              );
              isetcookie('is_login','1',3600*24*365);
              isetcookie('userphone',$userinfo['phone'],3600*24*365,$httponly = true);
              isetcookie('username',$userinfo['name'],3600*24*365,$httponly = true);
              isetcookie('user_id',$userinfo['id'],3600*24*365,$httponly = true);
              echo json_encode($data);
            }else{
              $data = array(
                'message' => '登录失败，密码输入错误',
                'success' => 2
              );
              echo json_encode($data);
            }
          }else{
            $data = array(
              'message' => '登录失败,该用户不存在',
              'success' => 3
            );
            echo json_encode($data);
          }
        }else{
          $data = array(
            'message' => '登录失败，网络请求错误',
            'success' => 0
          );
          echo json_encode($data);
        }
    }
    // APPOINTMENT
    public function doMobileAppoint(){
      global $_W,$_GPC;
      $name = $_GPC['name'];
      $phone = $_GPC['phone'];
      $area1 = $_GPC['area1'];
      $area2 = $_GPC['area2'];
      $from = $_GPC['from'];
      $type = $_GPC['type'];
      if(!empty($name) && !empty($phone) && !empty($from)){
        $data = array(
          'uniacid' => $_W['uniacid'],
          'name' => $name,
          'phone' => $phone,
          'area1' => $area1,
          'area2' => $area2,
          'type' => $type,
          'from' => $from,
          'createtime' => time(),
          'status' => 0
        );
        $result = pdo_insert('bu_appoint',$data);
        if(!empty($result)){
          $fmdata = array(
            'message' => '预约成功',
            'status' => 1
          );
          echo json_encode($fmdata);
        }else{
          $fmdata = array(
            'message' => '预约失败',
            'status' => 0
          );
          echo json_encode($fmdata);
        }
      }
    }
    // FORGET
    public function doMobileForget(){
      global $_W,$_GPC;
      $phone = $_GPC['phone'];
      $code = $_GPC['code'];
      $password = $_GPC['password'];
      $rcode = pdo_fetch(' SELECT * FROM '.tablename('bu_usercode').' WHERE uniacid=:uniacid and userId=:userId LIMIT 1',array(':uniacid'=>$_W['uniacid'],':userId'=>$phone));
      if($code == $rcode['code']){
        $content = pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and phone=:phone LIMIT 1',array(':uniacid'=>$_W['uniacid'],':phone'=>$phone));
        $uid = pdo_fetch(' SELECT * FROM '.tablename('mc_members').' WHERE uniacid=:uniacid and mobile=:mobile LIMIT 1',array(':uniacid'=>$_W['uniacid'],':mobile'=>$phone));
        $data['password'] = authcode($password,'ENCODE');
        $result = pdo_update('bu_user',$data,array('id'=>$content['id']));
        $memberResult = pdo_update('mc_members',$data,array('uid'=>$uid['uid']));
        if(!empty($result)){
          $fmdata = array(
            'message' => '修改成功',
            'success' => 1
          );
          echo json_encode($fmdata);
        }else{
          $fmdata = array(
            'message' => '修改失败',
            'success' => 0
          );
          echo json_encode($fmdata);
        }
      }else{
        $fmdata = array(
          'message' => '验证码错误',
          'success' => 0
        );
        echo json_encode($fmdata);
      }
    }
    // Change
    public function doMobileChange(){
      global $_W,$_GPC;
      $oriphone = $_GPC['oriphone'];
      $nowphone = $_GPC['nowphone'];
      $code = $_GPC['code'];
      $password = $_GPC['password'];
      $rcode = pdo_fetch(' SELECT * FROM '.tablename('bu_usercode').' WHERE uniacid=:uniacid and userId=:userId LIMIT 1',array(':uniacid'=>$_W['uniacid'],':userId'=>$nowphone));
      if($code == $rcode['code']){
        $content = pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and phone=:phone LIMIT 1',array(':uniacid'=>$_W['uniacid'],':phone'=>$oriphone));
        $oripasswrod = authcode($content['password'],'DECODE');
        if($password == $oripasswrod){
          $uid = pdo_fetch(' SELECT * FROM '.tablename('mc_members').' WHERE uniacid=:uniacid and mobile=:mobile LIMIT 1',array(':uniacid'=>$_W['uniacid'],':mobile'=>$oriphone));
          $data['phone'] = $nowphone;
          $mdata['mobile'] = $nowphone;
          $result = pdo_update('bu_user',$data,array('id'=>$content['id']));
          $memberResult = pdo_update('mc_members',$mdata,array('uid'=>$uid['uid']));
          if(!empty($result)){
            $fmdata = array(
              'message' => '修改成功',
              'success' => 1
            );
            echo json_encode($fmdata);
          }else{
            $fmdata = array(
              'message' => '修改失败，原用户不存在',
              'success' => 0
            );
            echo json_encode($fmdata);
          }
        }else{
          $fmdata = array(
            'message' => '手机号或者密码错误',
            'success' => 0
          );
          echo json_encode($fmdata);
        }
      }else{
        $fmdata = array(
          'message' => '验证码错误',
          'success' => 0
        );
        echo json_encode($fmdata);
      }
    }
    public function doMobileCheck(){
      global $_W,$_GPC;
      $phone = $_GPC['phone'];
      if(!empty($phone)){
        $result = pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and phone=:phone LIMIT 1',array(':uniacid'=>$_W['uniacid'],':phone'=>$phone));
        if(!empty($result)){
          $fmdata = array(
            'message' => '该用户已注册',
            'success' => 1
          );
          echo json_encode($fmdata);
        }else{
          $fmdata = array(
            'message' => '该手机号未注册',
            'success' => 0
          );
          echo json_encode($fmdata);
        }
      }else{
        $fmdata = array(
          'message' => '手机号为空',
          'success' => -9
        );
        echo json_encode($fmdata);
      }
    }
    // register
    public function doMobileRegister(){
      global $_W,$_GPC;
      $types = array('register','sendSms','check');
      $type = in_array($_GPC['type'],$types) ? $_GPC['type']:'';
      if($type == 'sendSms'){
        $yzmType = $_GPC['yzmType'];
        $code = '';
        for ($i=0; $i < 6; $i++) {
          $code .= rand(0,9);
        }
        $Sms = new Sms();
        $phone = $_GPC['phone'];
        $params = array(
          [
            'name' => 'code',
            'value' => $code
          ]
        );
        $status = $Sms::sendSms($phone,$params,$yzmType);
        if(!empty($status)){
          $content = pdo_fetch(' SELECT * FROM '.tablename('bu_usercode').' WHERE uniacid=:uniacid and userId=:userId',array(':uniacid'=>$_W['uniacid'],':userId'=>$phone));
          if(!empty($content)){
            $data = array(
              'code' => $code,
              'createtime' => time()
            );
            $result = pdo_update('bu_usercode',$data,array('id'=>$content['id']));
            if(!empty($result)){
              $fmdata = array(
                'message' => $status,
                'success'=>1
              );
              echo json_encode($fmdata);
            }
          }else{
            $data = array(
              'uniacid' => $_W['uniacid'],
              'userId' => $phone,
              'code' => $code,
              'createtime' => time()
            );
            $result = pdo_insert('bu_usercode',$data);
            if(!empty($result)){
              $fmdata = array(
                'message' => $status,
                'success'=>1
              );
              echo json_encode($fmdata);
            }
          }
        }else{
          $fmdata = array(
            'message' => $status,
            'success' => 0
          );
          echo json_encode($fmdata);
        }
      }else if($type == 'check'){
        $phone = $_GPC['phone'];
        $result = pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and phone=:phone LIMIT 1',array(':uniacid'=>$_W['uniacid'],':phone'=>$phone));
        if(!empty($result)){
          $fmdata = array(
            'message' => '该手机号已注册',
            'success' => 0
          );
          echo json_encode($fmdata);
        }else{
          $fmdata = array(
            'message' => '该手机号可以使用',
            'success' => 1
          );
          echo json_encode($fmdata);
        }
      }else if($type == 'register'){
        $name = $_GPC['name'];
        $phone = $_GPC['phone'];
        $password = $_GPC['password'];
        $code = $_GPC['code'];
        if(!empty($phone)){
          $rcode = pdo_fetch(' SELECT * FROM '.tablename('bu_usercode').' WHERE uniacid=:uniacid and userId=:userId LIMIT 1',array(':uniacid'=>$_W['uniacid'],':userId'=>$phone));
          if($rcode['status'] == 0){
            if($code == $rcode['code']){
              $data = array(
                'uniacid'=>$_W['uniacid'],
                'name'=>$name,
                'phone'=>$phone,
                'password'=>authcode($password,'ENCODE'),
                'registerTime'=>time()
              );
              $groupid = pdo_fetch(' SELECT * FROM '.tablename('mc_groups').' WHERE uniacid=:uniacid and isdefault=:isdefault',array(':uniacid'=>$_W['uniacid'],':isdefault'=>1));
              $memberData = array(
                'uniacid'=>$_W['uniacid'],
                'mobile' => $phone,
                'password' => authcode($password,'ENCODE'),
                'groupid' => $groupid['groupid'],
                'createtime' => time(),
                'realname' => $name
              );
              pdo_update('bu_usercode',array('status'=>1),array('id'=>$rcode['id']));
              $result = pdo_insert('bu_user',$data);
              $member = pdo_insert('mc_members',$memberData);

              if(!empty($result) && !empty($member)){
                $userid = pdo_insertid();
                $fmdata = array(
                  'success' => 1,
                  'message' => '注册成功',
                  'userid' => $userid,
                  'name' => $name,
                  'phone' => $phone
                );
                echo json_encode($fmdata);
              }else{
                $fmdata = array(
                  'success' => 0,
                  'message' => '注册失败，请重试'
                );
                echo json_encode($fmdata);
              }
            }else{
              $fmdata = array(
                'success' => 0,
                'message' => '验证码不正确'
              );
              echo json_encode($fmdata);
            }
          }else{
            $fmdata = array(
              'success' => 0,
              'message' => '该验证码已使用过，请重新发送'
            );
            echo json_encode($fmdata);
          }
        }
      }
    }

    public function doMobileQuestion(){
      global $_W,$_GPC;
      $themeid = $_GPC['themeid'];
      $result = $_GPC['result'];
      $childId = $_GPC['childId'];
      $userDetail = pdo_fetch(' SELECT * FROM '.tablename('bu_oprecord').' WHERE uniacid=:uniacid and username=:username and userphone=:userphone',array(':uniacid'=>$_W['uniacid'],':username'=>$_GPC['username'],':userphone'=>$_GPC['userphone']));
      $data = array(
        'uniacid' => $_W['uniacid'],
        'themeid' => $themeid,
        'username' => $_GPC['username'],
        'userphone' => $_GPC['userphone'],
        'options' => iserializer($_GPC['options']),
        'result' => $result,
        'childId' => $childId,
        'status' => 0,
        'createtime' => time()
      );
      $ranger = pdo_fetchall(' SELECT * FROM '.tablename('bu_ranger').' WHERE uniacid=:uniacid and themeid=:themeid',array(':uniacid'=>$_W['uniacid'],':themeid'=>$themeid));
      foreach ($ranger as $key => $value) {
        if($value['rang1']<= $result && $result <= $value['rang2']){
          if(!empty($userDetail)){
            $result = pdo_update('bu_oprecord',$data,array('id'=>$userDetail['id']));
            $userid = pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and phone=:phone LIMIT 1',array(':uniacid'=>$_W['uniacid'],':phone'=>$_GPC['userphone']));
            $isDate['isQuestion'] = 1;
            $isQuestion = pdo_update('bu_user',$isDate,array('id'=>$userid['id']));
          }else{
            $result = pdo_insert('bu_oprecord',$data);
            $userid = pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE uniacid=:uniacid and phone=:phone LIMIT 1',array(':uniacid'=>$_W['uniacid'],':phone'=>$_GPC['userphone']));
            $isDate['isQuestion'] = 1;
            $isQuestion = pdo_update('bu_user',$isDate,array('id'=>$userid['id']));
          }
          if(!empty($result) && !empty($isQuestion)){
            $fmdata = array(
              'message' => '提交成功',
              'success' => 1,
              'result' =>$value['result'],
              'resultLevel' => $value['resultLevel']
            );
            echo json_encode($fmdata);
          }else{
            $fmdata = array(
              'message' => '提交失败',
              'success' => 0,
            );
            echo json_encode($fmdata);
          }
        }
      }
    }
    // php导入excel
   function Imexcel($file){
     global $_W, $_GPC;
      $tmp_file = $file['tmp_name'];
      $file_types = explode(".", $file['name']);
      $file_type = $file_types [count($file_types) - 1];
      if (strtolower($file_type) != "xls" && strtolower($file_type) != "xlsx") {
        message('文件格式错误（不是Excel文件）');
      }
      $savePath = IA_ROOT.'/excel/';
      $str = 'lastExcel';
      $file_name = $str. "." . $file_type;
      $uploadfile=$savePath.$file_name;
      //return $file_name;
      $objPHPExcel =move_uploaded_file($tmp_file,$uploadfile);
      $objReader = PHPExcel_IOFactory::createReader('Excel2007');
      $objPHPExcel = PHPExcel_IOFactory::load($uploadfile);
      $sheet = $objPHPExcel->getSheet(0);
      $highestRow = $sheet->getHighestRow(); // 取得总行数
      $highestColumn = $sheet->getHighestColumn(); // 取得总列数
      //$obj_PHPExcel =$objReader->load($uploadfile, $encode = 'utf-8');  //加载文件内容,编码utf-8
      $userinfo = array();
      for($j=2;$j<=$highestRow;$j++){
           for($k='A';$k<=$highestColumn;$k++){
               // $std .= iconv("UTF-8","utf-8",$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue()).'\\';//读取单元格
               $StringList = array(
                 'A'=>'name',
                 'B'=>'phone',
                 'C'=>'gettype',
                 'D'=>'date',
                 'E'=>'title',
                 'F'=>'num',
                 'G'=>'money',
                 'H'=>'datetime'
               );
               $userinfo[$j][$StringList[$k]] = iconv("UTF-8","utf-8",$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue());
           }

           if($userinfo[$j]['gettype'] != 1 && !empty($userinfo[$j]['gettype']) && empty($userinfo[$j]['money']) && empty($userinfo[$j]['datetime'])){
             if (!empty($userinfo[$j]['name']) && !empty($userinfo[$j]['phone']) && !empty($userinfo[$j]['gettype']) && !empty($userinfo[$j]['num']) ) {
               $strs = explode("\\",$std);
               $credit=pdo_fetch('SELECT * FROM '.tablename('mc_members').'WHERE uniacid =:uniacid and mobile =:mobile ',array(':uniacid'=>$_W['uniacid'],':mobile'=>$userinfo[$j]['phone'] ));
               //var_dump($credit);
               if (empty($credit)) {
                 $name=$userinfo[$j]['name'];
                 $wordmessage = '用户'.$name.'不存在';
                 message($wordmessage);
               }
               $integral = $credit['credit1']+$userinfo[$j]['num'];
               $data = array('credit1'=>$integral);
               pdo_update('mc_members', $data, array('uniacid'=>$_W['uniacid'], 'uid'=>$credit['uid']));
               $remarks=array(
                 'gettype'=>$userinfo[$j]['gettype'],
                 'content'=>$userinfo[$j]['title'],
               );
               $remark=iserializer($remarks);
               // 需插入数据库的数据
               $time=$userinfo[$j]['date'];
               $time = ($time-25569)*24*60*60;
               $date=array(
                 'uid' => $credit['uid'],
                 'uniacid' => $_W['uniacid'],
                 'credittype'=>'credit1',
                 'num'=>$userinfo[$j]['num'],
                 'remark'=>$remark,
                 'createtime' => time(),
                 'operator'=>$_W['uid'],
                 'date'=>$time
               );
               $insert=pdo_insert('mc_credits_record',$date);
             }else{
               message('上传格式错误');
             }
           }elseif($userinfo[$j]['gettype']==1){
             if (!empty($userinfo[$j]['name']) && !empty($userinfo[$j]['phone']) && !empty($userinfo[$j]['gettype']) && !empty($userinfo[$j]['num'])&& !empty($userinfo[$j]['money']) && !empty($userinfo[$j]['datetime']) ) {
               $strs = explode("\\",$std);
               $credit=pdo_fetch('SELECT * FROM '.tablename('mc_members').'WHERE uniacid =:uniacid and mobile =:mobile ',array(':uniacid'=>$_W['uniacid'],':mobile'=>$userinfo[$j]['phone'] ));
               if (empty($credit)) {
                 $name=$userinfo[$j]['name'];
                 $wordmessage = '用户：'.$name.'不存在';
                 message($wordmessage);
               }
               $integral = $credit['credit1']+$userinfo[$j]['num'];
               $data = array('credit1'=>$integral);
               pdo_update('mc_members', $data, array('uniacid'=>$_W['uniacid'], 'uid'=>$credit['uid']));
               $productid=pdo_fetch('SELECT id FROM '.tablename('bu_product').'WHERE uniacid =:uniacid and productTitle = :title',array(':uniacid'=>$_W['uniacid'],':title'=>$userinfo[$j]['title'] ));
               if(empty($productid)){
                 $name=$userinfo[$j]['title'];
                 $wordmessage = '产品：'.$name.'不存在';
                 message($wordmessage);
               }
               $remarks=array(
                 'gettype'=>$userinfo[$j]['gettype'],
                 'productid'=>$productid['id'],
                 'title'=>$userinfo[$j]['title'],
                 'datetime' => $userinfo[$j]['datetime'],
                 'num'=>$userinfo[$j]['money'],
               );
               $remark=iserializer($remarks);
               // 需插入数据库的数据
               $time=$userinfo[$j]['date'];
               $time = ($time-25569)*24*60*60;
              // $dates=strtotime($userinfo[$j]['date']);
               $date=array(
                 'uid' => $credit['uid'],
                 'uniacid' => $_W['uniacid'],
                 'credittype'=>'credit1',
                 'num'=>$userinfo[$j]['num'],
                 'remark'=>$remark,
                 'createtime' => time(),
                 'operator'=>$_W['uid'],
                 'date'=>$time
               );
               $insert=pdo_insert('mc_credits_record',$date);
             }else{
               message('请输入正确格式');
             }

           }
         }
         if(!empty($insert)){
           message('导入成功');
         }else{
           message('导入失败');
         }
         return $userinfo;
    }

    // php导出excel
    function Ipexcel($contents,$type){
      global $_W,$_GPC;
      $objPHPExcel = new PHPExcel();
      // // //创建人
      $objPHPExcel->getProperties()->setCreator("xc") //创建人
      							 ->setLastModifiedBy("xc")  // 最后修改人
      							 ->setTitle("Office 2007 XLSX Test Document") //标题
      							 ->setSubject("Office 2007 XLSX Test Document") //题目
      							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.") //描述
      							 ->setKeywords("office 2007 openxml php") //关键字
      							 ->setCategory("Test result file"); //种类
      // // Add some data
      if($type == 'all'){
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '姓名')
                    ->setCellValue('B1', '联系方式')
                    ->setCellValue('C1', '获取积分方式')
                    ->setCellValue('D1', '日期')
                    ->setCellValue('E1', '产品名')
                    ->setCellValue('F1', '投资金额')
                    ->setCellValue('G1', '期限');
        foreach ($contents as $key => $value) {
          $uid = pdo_fetch(' SELECT `uid` FROM '.tablename('mc_members').' WHERE uniacid=:uniacid and mobile=:mobile',array(':uniacid'=>$_W['uniacid'],':mobile'=>$value['phone']));
          $userinfo = pdo_fetchall(' SELECT * FROM '.tablename('mc_credits_record').' WHERE uniacid=:uniacid and uid=:uid',array(':uniacid'=>$_W['uniacid'],':uid'=>$uid['uid']));
          // 用户姓名以及联系方式
          $key = $key+2;
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$key, $value['name'])
                      ->setCellValue('B'.$key, $value['phone']);
          // 用户记录
          foreach ($userinfo as $key1 => $val) {
            $cell = ord("C");
            $cellFirst = $cell + 2*($key1);
            $cellSecond = $cell + (2*$key1) + 1;

            if($cellFirst > 90 || $cellSecond > 90){

                $rest = floor(($cellFirst - 90) / 26);
                $newCell = ord("A") + $rest;//65
                $newCell1 = ord("@");//64
                $newCellFirst = $newCell1 + ($cellFirst % 90) - floor(($cellFirst % 90)/26) * 26;
                $newCellSecond = $newCell1 + ($cellSecond % 90) - floor((($cellSecond % 90)-1)/26) * 26;
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue(chr($newCell).chr($newCellFirst).'1', '积分')
                            ->setCellValue(chr($newCell).chr($newCellSecond).'1','购买项目');

                $valist = iunserializer($val['remark']);

                if(!empty($valist['productname'])){
                  $objPHPExcel->setActiveSheetIndex(0)
                              ->setCellValue(chr($newCell).chr($newCellFirst).$key, $val['num'])
                              ->setCellValue(chr($newCell).chr($newCellSecond).$key, $valist['productname']);
                }else{
                  if(!empty($valist['productid'])){
                    $productname = pdo_fetch(' SELECT * FROM '.tablename('bu_product').' WHERE uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$valist['productid']));
                  }else{
                    $productname['productTitle'] = '系统赠送';
                  }

                  $objPHPExcel->setActiveSheetIndex(0)
                              ->setCellValue(chr($newCell).chr($newCellFirst).$key, $val['num'])
                              ->setCellValue(chr($newCell).chr($newCellSecond).$key, $productname['productTitle']);
                }

            }else{
              $objPHPExcel->setActiveSheetIndex(0)
                          ->setCellValue(chr($cellFirst).'1', '积分')
                          ->setCellValue(chr($cellSecond).'1','购买项目');

              $valist = iunserializer($val['remark']);

              if(!empty($valist['productname'])){
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue(chr($cellFirst).$key, $val['num'])
                            ->setCellValue(chr($cellSecond).$key, $valist['productname']);
              }else{
                if(!empty($valist['productid'])){
                  $productname = pdo_fetch(' SELECT * FROM '.tablename('bu_product').' WHERE uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$valist['productid']));
                }else{
                  $productname['productTitle'] = '系统赠送';
                }
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue(chr($cellFirst).$key, $val['num'])
                            ->setCellValue(chr($cellSecond).$key, $productname['productTitle']);
              }
            }
          }
        }
      }else{
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '姓名')
                    ->setCellValue('B1', '联系方式');
        foreach ($contents as $key => $value) {
          $uid = pdo_fetch(' SELECT `uid` FROM '.tablename('mc_members').' WHERE uniacid=:uniacid and mobile=:mobile',array(':uniacid'=>$_W['uniacid'],':mobile'=>$value['phone']));
          $userinfo = pdo_fetchall(' SELECT * FROM '.tablename('mc_credits_record').' WHERE uniacid=:uniacid and uid=:uid',array(':uniacid'=>$_W['uniacid'],':uid'=>$uid['uid']));
          // 用户姓名以及联系方式
          $key = $key+2;
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$key, $value['name'])
                      ->setCellValue('B'.$key, '***********');
          // 用户记录
          foreach ($userinfo as $key1 => $val) {
            $cell = ord("C");
            $cellFirst = $cell + 2*($key1);
            $cellSecond = $cell + (2*$key1) + 1;
            if($cellFirst > 90 || $cellSecond > 90){
                $rest = floor(($cellFirst - 90) / 26);
                $newCell = ord("A") + $rest;//65
                $newCell1 = ord("@");//64
                $newCellFirst = $newCell1 + ($cellFirst % 90) - floor(($cellFirst % 90)/26) * 26;
                $newCellSecond = $newCell1 + ($cellSecond % 90) - floor((($cellSecond % 90)-1)/26) * 26;
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue(chr($newCell).chr($newCellFirst).'1', '积分')
                            ->setCellValue(chr($newCell).chr($newCellSecond).'1','购买项目');

                $valist = iunserializer($val['remark']);

                if(!empty($valist['productname'])){
                  $objPHPExcel->setActiveSheetIndex(0)
                              ->setCellValue(chr($newCell).chr($newCellFirst).$key, $val['num'])
                              ->setCellValue(chr($newCell).chr($newCellSecond).$key, $valist['productname']);
                }else{
                  if(!empty($valist['productid'])){
                    $productname = pdo_fetch(' SELECT * FROM '.tablename('bu_product').' WHERE uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$valist['productid']));
                  }else{
                    $productname['productTitle'] = '系统赠送';
                  }
                  $objPHPExcel->setActiveSheetIndex(0)
                              ->setCellValue(chr($newCell).chr($newCellFirst).$key, $val['num'])
                              ->setCellValue(chr($newCell).chr($newCellSecond).$key, $productname['productTitle']);
                }
            }else{
              $objPHPExcel->setActiveSheetIndex(0)
                          ->setCellValue(chr($cellFirst).'1', '积分')
                          ->setCellValue(chr($cellSecond).'1','购买项目');

              $valist = iunserializer($val['remark']);

              if(!empty($valist['productname'])){
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue(chr($cellFirst).$key, $val['num'])
                            ->setCellValue(chr($cellSecond).$key, $valist['productname']);
              }else{
                if(!empty($valist['productid'])){
                  $productname = pdo_fetch(' SELECT * FROM '.tablename('bu_product').' WHERE uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$valist['productid']));
                }else{
                  $productname['productTitle'] = '系统赠送';
                }
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue(chr($cellFirst).$key, $val['num'])
                            ->setCellValue(chr($cellSecond).$key, $productname['productTitle']);
              }
            }
          }

        }
      }

      // Rename worksheet
      $objPHPExcel->getActiveSheet()->setTitle('用户信息');


      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $objPHPExcel->setActiveSheetIndex(0);


      // Redirect output to a client’s web browser (Excel2007)
      ob_end_clean();//清除缓冲区,避免乱码
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="用户信息.xlsx"');
      header('Cache-Control: max-age=0');
      // If you're serving to IE 9, then the following may be needed
      header('Cache-Control: max-age=1');

      // If you're serving to IE over SSL, then the following may be needed
      header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
      header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
      header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      header ('Pragma: public'); // HTTP/1.0

      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
      exit;
    }
    function mb_str_split( $string ) {
      // /u表示把字符串当作utf-8处理，并把字符串开始和结束之前所有的字符串分割成数组
      return preg_split('/(?<!^)(?!$)/u', $string );
    }
    // 判断是否是移动端
    function isMobile(){
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA']))
        {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT']))
        {
            $clientkeywords = array ('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
                );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT']))
        {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            }
        }
        return false;
    }
    // 判断是否是微信浏览器
    function is_weixin() {
      if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
          return true;
      } return false;
    }

    //绑定微信
    public function doMobileBd(){
      global $_W,$_GPC;
      $userid = $_GPC['user_id'];
      if(!empty($userid)){
        $uid = $_GPC['uid'];
        $data = array(
          'isbind'=>1,
          'uid'=>$uid
        );
        $inset=pdo_update('bu_user',$data,array('id'=>$userid));
        if ($inset) {
          $return = array(
            'status'=>1,
            'message'=>'绑定成功'
          );
        }else{
          $return=array(
            'status'=>0,
            'message'=>'绑定失败'
          );
        }
      }else{
        $return = array(
          'status' => 0,
          'message' => '没有找到指定的用户,绑定失败!'
        );
      }
      echo json_encode($return);
    }
    //认证
    public function doMobileRz(){
      global $_W,$_GPC;

      $info = pdo_fetch(' SELECT * FROM '.tablename('bu_user').' WHERE id=:id ',array(':id'=>$_GPC['user_id']));
      $identify=$_GPC['identifynum'];
      $cert=$_GPC['identifytype'];
      if(!empty($info)){
        $data=array(
            'cert'=>$cert,
            'identity'=>$identify,
            'isauth'=>1,
          );
        $inno=pdo_update('bu_user',$data,array('id'=>$_GPC['user_id']));
        if (!empty($inno)) {
          $return=array(
             'status'=>1,
             'message'=>'认证成功'
          );
        }else{
          $return=array(
             'status'=>0,
             'message'=>'认证失败'
          );
        }

      }else{
        $return=array(
            'status'=>0,
            'message'=>'没有找到指定用户'
          );
      }

      echo json_encode($return);
    }
    //三级联动
    public function doMobileArea(){
        global $_W,$_GPC;
        $type=$_GPC['type'];
        $uid=$_GPC['parent_id'];
        if($type==1){
          $zh=pdo_fetchall(" SELECT * FROM ".tablename('bu_region') . "  WHERE  `region_type` = $type" );
          $data=array(
            'data'=>$zh,
            'status'=>1,
            'message'=>'一级'
          );
        }else{
          if (!empty($uid) && $uid != '') {
            $zh=pdo_fetchall(" SELECT * FROM ".tablename('bu_region')." WHERE `region_type` = $type and `parent_id`=$uid ");
            $data=array(
              'data'=>$zh,
              'status'=>1,
              'message'=>'成功'
            );
          }else{
            $data=array(
              'status'=>0,
              'message'=>'失败'
            );
          }
        }
        echo json_encode($data);
    }

    //根据传值排序
     public function doMobileSort(){
      global $_W,$_GPC;
       $pcate= $_GPC['type'];
      //$pcate = 6;
       $ordertype=$_GPC['types'];
      //$ordertype = 1;
      $psize=4;
      $page =!empty(intval($_GPC['page'])) ? intval($_GPC['page']) : 0 ;
      if (!empty($page)) {
        $page = ($page-1) * $psize;
      }else {
        $page=0;
      }
      //$sum = pdo_fetchall("SELECT * FROM " . tablename('hc_credit_shopping_goods') . " WHERE  `uniacid`= '{$_W['uniacid']}'ORDER BY `id`DESC LIMIT ".$page.",".$psize);
      switch ($ordertype) {
        case 1:
          $zh=pdo_fetchall(" SELECT * FROM ".tablename('hc_credit_shopping_goods') . "  WHERE `weid` = '{$_W['uniacid']}' and  `pcate` = $pcate ORDER BY `marketprice` ASC , `createtime` DESC LIMIT ".$page.",".$psize);
          if (!empty($zh)) {
            $data = array(
              'data' =>$zh ,
              'status'=>1,
              'message'=>'成功'
             );
          }else{
            $data = array(
              'status'=>0,
              'message'=>'失败'
            );
          }
          break;
          case 2:
              $new= pdo_fetchall(" SELECT * FROM " . tablename('hc_credit_shopping_goods') . " WHERE `weid` = '{$_W['uniacid']}' and  `pcate` = $pcate ORDER BY `createtime` DESC LIMIT ".$page.",".$psize);

              if (!empty($new)) {
                $data = array(
                  'data' =>$new ,
                  'status'=>1,
                  'message'=>'成功'
                );
              }else{
                $data = array(
                  'status'=>0,
                  'message'=>'失败');
                }
            break;
          case 3:
              $dinte=pdo_fetchall("SELECT * FROM " . tablename('hc_credit_shopping_goods') . "  WHERE `weid` = '{$_W['uniacid']}' and  `pcate` = $pcate  ORDER BY `marketprice`DESC LIMIT ".$page.",".$psize);
              if (!empty($dinte)) {
                $data = array(
                  'data' =>$dinte ,
                  'status'=>1,
                  'message'=>'成功'
                );
              }else{
                $data = array(
                  'status'=>0,
                  'message'=>'失败'
                );
              }
            break;
          case 4:
              $inte=pdo_fetchall("SELECT * FROM " . tablename('hc_credit_shopping_goods') . " WHERE `weid` = '{$_W['uniacid']}' and  `pcate` = $pcate ORDER BY `marketprice`ASC LIMIT ".$page.",".$psize);
              if (!empty($inte)) {
                $data = array(
                  'data' =>$inte ,
                  'status'=>1,
                  'message'=>'成功'
                );
              }else{
                $data = array(
                  'status'=>0,
                  'message'=>'失败'
                );
              }
            break;
            default:
              echo json_encode('12332');
              break;
      }
      echo json_encode($data);
    }
    //增加收货地址
  	public function doMobileReceipt(){
  		global $_W,$_GPC;
        $isdefault=$_GPC['isdefault'];
        $uid=$_GPC['uid'];
        if ($isdefault == 1) {
          $address=pdo_fetchall("SELECT * FROM".tablename('bu_address')."WHERE uid=$uid and uniacid='".$_W['uniacid']."'");
          foreach ($address as $key => $value) {
              $data=array(
                'isdefault'=>0
              );
              $address=pdo_update('bu_address',$data,array('id'=>$value['id']));
          }
          $data=array(
          'uniacid'=>$_W['uniacid'],
          'uid'=>$uid,
          'username'=>$_GPC['ausername'],
          'phone'=>$_GPC['phone'],
          'mobile'=>$_GPC['mobile'],
          'exmobile'=>$_GPC['exmobile'],
          'mail'=>$_GPC['mail'],
          'province'=>$_GPC['province'],
          'city'=>$_GPC['city'],
          'district'=>$_GPC['district'],
          'address'=>$_GPC['address'],
          'isdefault'=>$_GPC['isdefault'],
          'creattime'=>time()
          );
            $info=pdo_insert('bu_address',$data);
            $id=pdo_insertid();
            if (!empty($info)){
              $data=array(
                'date'=>$data,
                'data'=>$id,
                'status'=>1,
                'message'=>'新增成功'
              );
            }else{
              $data=array(
                'status'=>0,
                'message'=>'新增失败'
              );
            }
        }else {
          $data=array(
          'uniacid'=>$_W['uniacid'],
          'uid'=>$uid,
          'username'=>$_GPC['ausername'],
          'phone'=>$_GPC['phone'],
          'mobile'=>$_GPC['mobile'],
          'exmobile'=>$_GPC['exmobile'],
          'mail'=>$_GPC['mail'],
          'province'=>$_GPC['province'],
          'city'=>$_GPC['city'],
          'district'=>$_GPC['district'],
          'address'=>$_GPC['address'],
          'isdefault'=>$_GPC['isdefault'],
          'creattime'=>time(),
          );
            $info=pdo_insert('bu_address',$data);
            $id=pdo_insertid();

            if (!empty($info)){
              $data=array(
                'date'=>$data,
                'data'=>$id,
                'status'=>1,
                'message'=>'新增成功'
              );
            }else{
              $data=array(
                'status'=>0,
                'message'=>'新增失败'
              );
            }
        }
        echo json_encode($data);
  	}
    //编辑
    public function doMobileEdit(){
      global $_W,$_GPC;
      $id=$_GPC['id'];
      if($_GPC['type'] == "" || empty($_GPC['type'])){
        $info=pdo_fetch("SELECT * FROM".tablename('bu_address')."WHERE uniacid=:uniacid and id=:id ",array(':uniacid'=>$_W['uniacid'],':id'=>$id));
        if (!empty($info)) {
          $data=array(
            'data'=>$info,
            'status'=>1,
            'message'=>'成功'
          );
        }else {
          $data=array(
            'status'=>0,
            'message'=>'请创建收货地址'
          );
        }
      }
      echo json_encode($data);
    }
    //编辑
    public function doMobileGx(){
      global $_W,$_GPC;
      $uid=$_GPC['uid'];
      $isdefault=$_GPC['isdefault'];
      if ($isdefault==1) {
        $address=pdo_fetchall("SELECT * FROM".tablename('bu_address')."WHERE uid=$uid and uniacid='".$_W['uniacid']."'");
        foreach ($address as $key => $value) {
            $data=array(
              'isdefault'=>0
            );
            $address=pdo_update('bu_address',$data,array('id'=>$value['id']));
        }
        $data=array(
          'uniacid'=>$_W['uniacid'],
          'uid'=>$uid,
          'username'=>$_GPC['username'],
          'phone'=>$_GPC['phone'],
          'mobile'=>$_GPC['mobile'],
          'exmobile'=>$_GPC['exmobile'],
          'mail'=>$_GPC['mail'],
          'province'=>$_GPC['province'],
          'city'=>$_GPC['city'],
          'district'=>$_GPC['district'],
          'address'=>$_GPC['address'],
          'isdefault'=>$isdefault,
        );
        $info=pdo_update('bu_address',$data,array('id'=>$_GPC['id']));
        if(!empty($info)){
          $data=array(
            'status'=>1,
            'message'=>'更新成功'
          );
        }else{
          $data=array(
            'status'=>0,
            'message'=>'更新失败'
          );
        }
      }else{
        $data=array(
          'uniacid'=>$_W['uniacid'],
          'uid'=>$uid,
          'username'=>$_GPC['username'],
          'phone'=>$_GPC['phone'],
          'mobile'=>$_GPC['mobile'],
          'exmobile'=>$_GPC['exmobile'],
          'mail'=>$_GPC['mail'],
          'province'=>$_GPC['province'],
          'city'=>$_GPC['city'],
          'district'=>$_GPC['district'],
          'address'=>$_GPC['address'],
          'isdefault'=>$isdefault,
        );
        $info=pdo_update('bu_address',$data,array('id'=>$_GPC['id']));
        if(!empty($info)){
          $data=array(
            'status'=>1,
            'message'=>'更新成功'
          );
        }else{
          $data=array(
            'status'=>0,
            'message'=>'更新失败'
          );
        }
      }
      echo json_encode($data);
    }


    //默认
    public function doMobileDefault(){
      global $_W,$_GPC;
      $id=$_GPC['id'];
      $uid=$_GPC['uid'];
      $address=pdo_fetchall("SELECT * FROM".tablename('bu_address')."WHERE uid=$uid and uniacid='".$_W['uniacid']."'");
      foreach ($address as $key => $value) {
          $data=array(
            'isdefault'=>0
          );
          $address=pdo_update('bu_address',$data,array('id'=>$value['id']));
      }
      $value=pdo_fetch("SELECT * FROM ".tablename('bu_address')."WHERE id=$id and uniacid='".$_W['uniacid']."'");
      $date=array('isdefault'=>1);
      $updata=pdo_update('bu_address',$date,array('id'=>$id));
      if(!empty($updata)){
        $data=array(
          'status'=>1,
          'message'=>'succ'
        );
      }else{
        $data=array(
          'status'=>0,
          'message'=>'fail'
        );
      }
      echo json_encode($data);
    }
    //删除地址
    public function doMobileDelete(){
      global $_W,$_GPC;
      $uid=$_GPC['id'];
      $info=pdo_delete('bu_address',array('id'=>$uid));
      if (!empty($info)) {
        $data=array(
          'status'=>1,
          'message'=>'删除成功'
        );
      }else{
        $data=array(
          'status'=>0,
          'message'=>'删除失败'
        );
      }
      echo json_encode($data);
    }
    //搜索
    public function doMobileSearch(){
      global $_W,$_GPC;
      $uid=$_GPC['uid'];
      $value=$_GPC['value'];
      if (preg_match('/\d+/',$value)) {
        $where="WHERE o.uniacid='".$_W['uniacid']."' and o.uid=$uid and o.oid=$value ";
        $info = pdo_fetchall("select o.price,o.creattime, o.status, o.oid, a.title, b.username, b.province ,b.city ,b.district,b.address, b.phone from " . tablename("bu_order") . " o " . "left join " . tablename("hc_credit_shopping_goods") . " a " . " on o.pid=a.id   " . " join " . tablename("bu_address") . " b ". "on b.id=o.aid  "." $where ORDER BY o.creattime DESC ");//LIMIT ".$page.",".$psiz
        foreach ($info as $key => $value) {
        for($i=0;$i<1;$i++){
             $times.= rand(0,9);
          }
            $info[$key]['updatetime']=date('Y-m-d H:i:s',$info[$key]['creattime']+$times);
            $info[$key]['creattime']=date('Y-m-d H:i:s',$info[$key]['creattime']);
        }
        $result=array(
          'data'=>$info,
          'status'=>1,
          'message'=>'名称查询'
        );
      }else{
        $where="WHERE o.uniacid='".$_W['uniacid']."' and o.uid=$uid and a.title like '%".$value."%' ";
        $info = pdo_fetchall("select o.price,o.creattime, o.status, o.oid, a.title, b.username, b.province ,b.city ,b.district,b.address, b.phone from " . tablename("bu_order") . " o " . "left join " . tablename("hc_credit_shopping_goods") . " a " . " on o.pid=a.id   " . " join " . tablename("bu_address") . " b ". "on b.id=o.aid  "." $where ORDER BY o.creattime DESC ");//LIMIT ".$page.",".$psize
        foreach ($info as $key => $value) {
          for($i=0;$i<1;$i++){
             $times.= rand(0,9);
           }
          $info[$key]['updatetime']=date('Y-m-d H:i:s',$info[$key]['creattime']+$times);
          $info[$key]['creattime']=date('Y-m-d H:i:s',$info[$key]['creattime']);
        }
        $result=array(
          'data'=>$info,
          'status'=>1,
          'message'=>'名称查询'
        );
      }
      echo json_encode($result);
    }
    //分类
    public function doMobileCode(){
      global $_W,$_GPC;
      $uid=$_GPC['uid'];
      $cid=$_GPC['cid'];
      $status=$_GPC['status'];
      if ($cid == ''|| empty($cid)) {
        if ($status==0) {
          $where = "WHERE o.status=0 AND o.uniacid ='".$_W['uniacid']."' and o.uid=$uid  ";
          $info = pdo_fetchall("select o.price,o.creattime, o.status, o.oid, a.title, b.username, b.province ,b.city ,b.district,b.address, b.phone from " . tablename("bu_order") . " o " . "left join " . tablename("hc_credit_shopping_goods") . " a " . " on o.pid=a.id   " . " join " . tablename("bu_address") . " b ". "on b.id=o.aid  "." $where ORDER BY o.creattime DESC ");//LIMIT ".$page.",".$psize
          foreach ($info as $key => $value) {
            for($i=0;$i<1;$i++){
               $times.= rand(0,9);
             }
            $info[$key]['updatetime']=date('Y-m-d H:i:s',$info[$key]['creattime']+$times);
              $info[$key]['creattime']=date('Y-m-d H:i:s',$info[$key]['creattime']);
          }
          $result=array(
            'data'=>$info,
            'status'=>1,
            'message'=>'待发货'
          );
        }elseif ($status==1) {
          $where="WHERE o.status=1 AND o.uniacid ='".$_W['uniacid']."' and o.uid=$uid";
          $info=pdo_fetchall("select o.price,o.creattime, o.status, o.oid, a.title, b.username, b.province ,b.city ,b.district,b.address, b.phone from  " . tablename("bu_order") . "o"  . " left join " . tablename("hc_credit_shopping_goods") . " a "."on a.id=o.pid  "." join " . tablename("bu_address") . " b ". "on b.id=o.aid  "." $where ORDER BY o.creattime DESC ");
          foreach ($info as $key => $value) {
            for($i=0;$i<1;$i++){
               $times.= rand(0,9);
             }
            $info[$key]['updatetime']=date('Y-m-d H:i:s',$info[$key]['creattime']+$times);
            $info[$key]['creattime']=date('Y-m-d H:i:s',$info[$key]['creattime']);
          }
          $result=array(
            'data'=>$info,
            'status'=>1,
            'message'=>'已完成'
          );
        }else if($status==2){
          $where="WHERE a.weid = '".$_W['uniacid']."' and o.uid=$uid";
          $info=pdo_fetchall("select o.price,o.creattime, o.status, o.oid, a.title, b.username, b.province ,b.city ,b.district,b.address, b.phone from " . tablename("bu_order") . "o"  . " left join " . tablename("hc_credit_shopping_goods") . " a "."on a.id=o.pid  "." join " . tablename("bu_address") . " b ". "on b.id=o.aid  "." $where ORDER BY o.creattime DESC ");//LIMIT ".$page.",".$psize
          foreach ($info as $key => $value) {
            for($i=0;$i<1;$i++){
               $times.= rand(0,9);
             }
            $info[$key]['updatetime']=date('Y-m-d H:i:s',$info[$key]['creattime']+$times);
            $info[$key]['creattime']=date('Y-m-d H:i:s',$info[$key]['creattime']);
          }
          $result=array(
            'data'=>$info,
            'status'=>1,
            'message'=>'全部订单'
          );
        }
      }else if($status==''){
        if($cid == 0 ){
                $where="WHERE o.uniacid ='".$_W['uniacid']."' and o.uid=$uid";
                $all=pdo_fetchall("select o.price,o.creattime, o.oid, o.status, a.title, b.username, b.province ,b.city ,b.district,b.address, b.phone from  " . tablename("bu_order") . "o"  . " left join " . tablename("hc_credit_shopping_goods") . " a ". "on a.id=o.pid  "." join " . tablename("bu_address") . " b ". "on b.id=o.aid  "." $where ORDER BY o.creattime DESC ");
                for($i=0;$i<1;$i++){
                   $times.= rand(0,9);
                 }
                $info[$key]['updatetime']=date('Y-m-d H:i:s',$info[$key]['creattime']+$times);
                  $info[$key]['creattime']=date('Y-m-d H:i:s',$info[$key]['creattime']);
                }
                $result=array(
                  'data'=>$all,
                  'status'=>1,
                  'message'=>'全部类型'
                );

        }else if($cid != ''){
            $where="WHERE a.pcate=$cid  AND a.weid = '".$_W['uniacid']."' and o.uid=$uid";
            $info=pdo_fetchall("select o.price,o.creattime, o.oid, o.status, a.title, b.username, b.province ,b.city ,b.district,b.address, b.phone from  " . tablename("bu_order") . "o"  . " left join " . tablename("hc_credit_shopping_goods") . " a "."on a.id=o.pid  "." join " . tablename("bu_address") . " b ". "on b.id=o.aid  "." $where ORDER BY o.creattime DESC ");
            foreach ($info as $key => $value) {
              for($i=0;$i<1;$i++){
                 $times.= rand(0,9);
               }
              $info[$key]['updatetime']=date('Y-m-d H:i:s',$info[$key]['creattime']+$times);
              $info[$key]['creattime']=date('Y-m-d H:i:s',$info[$key]['creattime']);
            }
            $result=array(
              'data'=>$info,
              'status'=>1,
              'message'=>'其他类型'
            );
    }else if($cid !=''){
        if ($status==0){
          $where = "WHERE o.status=0 AND o.uniacid ='".$_W['uniacid']."' and o.uid=$uid and o.cid= $cid ";
          $info = pdo_fetchall("select o.price,o.creattime, o.status, o.oid, a.title, b.username, b.province ,b.city ,b.district,b.address, b.phone from " . tablename("bu_order") . " o " . "left join " . tablename("hc_credit_shopping_goods") . " a " . " on o.pid=a.id   " . " join " . tablename("bu_address") . " b ". "on b.id=o.aid  "." $where ORDER BY o.creattime DESC ");//LIMIT ".$page.",".$psize
          foreach ($info as $key => $value) {
            for($i=0;$i<1;$i++){
               $times.= rand(0,9);
             }
            $info[$key]['updatetime']=date('Y-m-d H:i:s',$info[$key]['creattime']+$times);
              $info[$key]['creattime']=date('Y-m-d H:i:s',$info[$key]['creattime']);
          }
          $result=array(
            'data'=>$info,
            'status'=>1,
            'message'=>'待发货'
          );
        }elseif ($status==1) {
          $where="WHERE o.status=1 AND o.uniacid ='".$_W['uniacid']."' and o.uid=$uid and o.cid= $cid";
          $info=pdo_fetchall("select o.price,o.creattime, o.status, o.oid, a.title, b.username, b.province ,b.city ,b.district,b.address, b.phone from  " . tablename("bu_order") . "o"  . " left join " . tablename("hc_credit_shopping_goods") . " a "."on a.id=o.pid  "." join " . tablename("bu_address") . " b ". "on b.id=o.aid  "." $where ORDER BY o.creattime DESC ");
          foreach ($info as $key => $value) {
            for($i=0;$i<1;$i++){
               $times.= rand(0,9);
             }
            $info[$key]['updatetime']=date('Y-m-d H:i:s',$info[$key]['creattime']+$times);
            $info[$key]['creattime']=date('Y-m-d H:i:s',$info[$key]['creattime']);
          }
          $result=array(
            'data'=>$info,
            'status'=>1,
            'message'=>'已完成'
          );
        }else if($status==2){
          $where="WHERE a.weid = '".$_W['uniacid']."' and o.uid=$uid and o.cid= $cid";
          $info=pdo_fetchall("select o.price,o.creattime, o.status, o.oid, a.title, b.username, b.province ,b.city ,b.district,b.address, b.phone from " . tablename("bu_order") . "o"  . " left join " . tablename("hc_credit_shopping_goods") . " a "."on a.id=o.pid  "." join " . tablename("bu_address") . " b ". "on b.id=o.aid  "." $where ORDER BY o.creattime DESC ");//LIMIT ".$page.",".$psize
          foreach ($info as $key => $value) {
            for($i=0;$i<1;$i++){
               $times.= rand(0,9);
             }
            $info[$key]['updatetime']=date('Y-m-d H:i:s',$info[$key]['creattime']+$times);
            $info[$key]['creattime']=date('Y-m-d H:i:s',$info[$key]['creattime']);
          }
          $result=array(
            'data'=>$info,
            'status'=>1,
            'message'=>'全部订单'
          );
        }
      }else if ($status != '') {
        if($cid == 0 ){
                $where="WHERE o.uniacid ='".$_W['uniacid']."' and o.uid=$uid and o.status=$status";
                $all=pdo_fetchall("select o.price,o.creattime, o.oid, o.status, a.title, b.username, b.province ,b.city ,b.district,b.address, b.phone from  " . tablename("bu_order") . "o"  . " left join " . tablename("hc_credit_shopping_goods") . " a ". "on a.id=o.pid  "." join " . tablename("bu_address") . " b ". "on b.id=o.aid  "." $where ORDER BY o.creattime DESC ");
                foreach ($info as $key => $value) {
                  for($i=0;$i<1;$i++){
                     $times.= rand(0,9);
                   }
                  $info[$key]['updatetime']=date('Y-m-d H:i:s',$info[$key]['creattime']+$times);
                  $info[$key]['creattime']=date('Y-m-d H:i:s',$info[$key]['creattime']);
                }
                $result=array(
                  'data'=>$all,
                  'status'=>1,
                  'message'=>'全部类型'
                );
        }else if($cid != ''){
            $where="WHERE a.pcate=$cid  AND a.weid = '".$_W['uniacid']."' and o.uid=$uid and o.status=$status";
            $info=pdo_fetchall("select o.price,o.creattime, o.oid, o.status, a.title, b.username, b.province ,b.city ,b.district,b.address, b.phone from  " . tablename("bu_order") . "o"  . " left join " . tablename("hc_credit_shopping_goods") . " a "."on a.id=o.pid  "." join " . tablename("bu_address") . " b ". "on b.id=o.aid  "." $where ORDER BY o.creattime DESC ");
            foreach ($info as $key => $value) {
              for($i=0;$i<1;$i++){
                 $times.= rand(0,9);
               }
              $info[$key]['updatetime']=date('Y-m-d H:i:s',$info[$key]['creattime']+$times);
              $info[$key]['creattime']=date('Y-m-d H:i:s',$info[$key]['creattime']);
            }
            $result=array(
              'data'=>$info,
              'status'=>1,
              'message'=>'其他类型'
            );
        }
      }else if($status == 0 ) {
        if($cid == 0 ){
                $where="WHERE o.uniacid ='".$_W['uniacid']."' and o.uid=$uid and o.status=0";
                $all=pdo_fetchall("select o.price,o.creattime, o.oid, o.status, a.title, b.username, b.province ,b.city ,b.district,b.address, b.phone from  " . tablename("bu_order") . "o"  . " left join " . tablename("hc_credit_shopping_goods") . " a ". "on a.id=o.pid  "." join " . tablename("bu_address") . " b ". "on b.id=o.aid  "." $where ORDER BY o.creattime DESC ");
                foreach ($info as $key => $value) {
                  for($i=0;$i<1;$i++){
                     $times.= rand(0,9);
                   }
                  $info[$key]['updatetime']=date('Y-m-d H:i:s',$info[$key]['creattime']+$times);
                  $info[$key]['creattime']=date('Y-m-d H:i:s',$info[$key]['creattime']);
                }
                $result=array(
                  'data'=>$all,
                  'status'=>1,
                  'message'=>'全部类型'
                );
        }else if($cid != ''){
            $where="WHERE a.pcate=$cid  AND a.weid = '".$_W['uniacid']."' and o.uid=$uid and o.status=0";
            $info=pdo_fetchall("select o.price,o.creattime, o.oid, o.status, a.title, b.username, b.province ,b.city ,b.district,b.address, b.phone from  " . tablename("bu_order") . "o"  . " left join " . tablename("hc_credit_shopping_goods") . " a "."on a.id=o.pid  "." join " . tablename("bu_address") . " b ". "on b.id=o.aid  "." $where ORDER BY o.creattime DESC ");
            foreach ($info as $key => $value) {
              for($i=0;$i<1;$i++){
                 $times.= rand(0,9);
               }
              $info[$key]['updatetime']=date('Y-m-d H:i:s',$info[$key]['creattime']+$times);
              $info[$key]['creattime']=date('Y-m-d H:i:s',$info[$key]['creattime']);
            }
            $result=array(
              'data'=>$info,
              'status'=>1,
              'message'=>'其他类型'
            );
        }
      }

      echo json_encode($result);
    }

    //商品积分获取
    public function doMobileShopDetail(){
      global $_W,$_GPC;
      $pid=$_GPC['proid'];
      $info=pdo_fetch( " SELECT * FROM ".tablename('hc_credit_shopping_goods')." WHERE `weid` = '{$_W['uniacid']}' AND id=$pid " );
      if (!empty($info)) {
        $data=array(
          'status'=>1,
          'data'=>$info
        );
      }else{
        $data=array(
          'status'=>0,
        );
      }
      echo json_encode($data);
    }
    //积分兑换
    public function doMobileExchange(){
  		global $_W,$_GPC;
      $config = $this->module['config'];
      $memberid= $_GPC['userid'];
      $aid = $_GPC['aid'];
      $pid = $_GPC['pid'];
      $mobile = $_GPC['mobile'];
  		$cost_credit = $_GPC['integral'];//积分
      // $member = pdo_fetch(" SELECT * FROM ".tablename('mc_members')." WHERE `mobile`=$mobile and uniacid='".$_W['uniacid']."'");
      $member = pdo_fetch(" SELECT * FROM ".tablename('mc_members').' WHERE uniacid=:uniacid and mobile=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$memberid));
  		$credit = $member['credit1'];
      if($credit == 0 || empty($credit)){
  		 $result = array(
         'status'=>0,
         'message'=>'您的积分不足'
       );
       echo json_encode($result);
       die();
  		}

  		// $cost_credit = $setting;

  	  $result_ticket = $credit - $cost_credit;

  		if($result_ticket < 0){

  		 $result=array(
         'status'=>0,
         'message'=>'您的积分不足'
       );
       echo json_encode($result);
       die();

  		}else{
  		 $member['credit1'] = $result_ticket;
  		 pdo_update('mc_members',$member,array('uid'=>$member['uid']));
       $name=pdo_fetch("SELECT * FROM ".tablename('hc_credit_shopping_goods')."WHERE id=$pid and weid='".$_W['uniacid']."'");
       $remarks = array(
         'productid'=>$name['id'],
         'productname'=>$name['title']
       );
       $remark=iserializer($remarks);
  		 //mc_credit_update($member['uid'],'credit1',-$cost_credit,iserializer($remark));
       $date=array(
         'uid'=>$member['uid'],
         'uniacid'=>$_W['uniacid'],
         'credittype'=>'credit1',
         'num'=>-$cost_credit,
         'operator'=>0,
         'createtime'=>time(),
         'remark'=>$remark,
         'clerk_id'=>0,
         'store_id'=>0,
         'clerk_type'=>1
       );
       $result = pdo_insert('mc_credits_record',$date);
       if(!empty($result)){
         $rid=pdo_insertid();
       }
       $time = $date['createtime'];
       $num=date('YmdHis',$time);
       for($i=0;$i<5;$i++){
          $num.= rand(0,9);
       }
       $data=array(
          'aid'=>$_GPC['aid'],
          'uid'=>$member['uid'],
          'uniacid'=>$_W['uniacid'],
          'pid'=>$_GPC['pid'],
          'cid'=>$_GPC['cid'],
          'price'=>$setting,
          'freight'=>$_GPC['freight'],
          'service'=>$_GPC['service'],
          'status'=>0,
          'creattime'=>time(),
          'oid'=>$num,
          'rid'=>$rid
        );
        $info=pdo_insert('bu_order',$data);
        $date=array(
          'weid'=>$_W['uniacid'],
          'from_user'=>$member['uid'],
          'ordersn'=>$num,
          'price'=>$setting,
          'status'=>1,
          'paytype'=>4,
          'addressid'=>$_GPC['aid'],
          'createtime'=>time(),
          'goodsid'=>$_GPC['pid'],
          'oid'=>$num
        );
        $infoid = pdo_insertid();
        $shopping_order=pdo_insert('hc_credit_shopping_order',$date);
        if (!empty($info)) {
          $member=pdo_fetch('SELECT mobile,credit1 FROM '.tablename('mc_members').'where uniacid=:uniacid and uid=:uid',array(':uniacid'=>$_W['uniacid'],':uid'=>$member['uid']));
          $phone=$member['mobile'];
          $users=pdo_fetch('SELECT isbind,uid FROM'.tablename('bu_user').'WHERE uniacid=:uniacid and phone=:phone',array(':uniacid'=>$_W['uniacid'],':phone'=>$phone));
          if ($users['isbind']==1) {
            $fid=$users['uid'];
            $fans=pdo_fetch('SELECT openid FROM '.tablename('mc_mapping_fans').'WHERE uniacid=:uniacid and uid=:uid',array(':uniacid'=>$_W['uniacid'],':uid'=>$fid));
            $openid=$fans['openid'];
            if($config['dxtoggle'] == 1){
              if(!empty($config['dhmb'])){
                $params = array(
                  [
                    'name'=>'title',
                    'value'=>$name['title']
                  ],
                  [
                    'name'=>'credit',
                    'value'=>$setting
                  ]
                );
                $result = $this->sendSmsMb($phone,$params,'zdy',$config['dhmb']);
                var_dump($result);
              }
            }
            if($config['mbtoggle'] == 1){
                if($_W['account']['level'] = ACCOUNT_SUBSCRIPTION_VERIFY) {
                  	$infos = "积分兑换通知\n";
                  	$infos.= "您在{$data['createtime']}进行对【{$name['title']}】商品积分兑换，使用{$setting}积分，积分余额【{$result_ticket}】积分。\n";
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
                    $result=array(
                      'orderid'=>$infoid,
                      'data'=>$info,
                      'status'=>1,
                      'message'=>'兑换成功'
                    );
                }
            }
         }else {
           $result=array(
             'orderid'=>$infoid,
             'data'=>$info,
             'status'=>1,
             'message'=>'兑换成功'
           );
         }
        }else{
          $result=array(
            'status'=>0,
            'message'=>'兑换失败'
          );
        }
        echo json_encode($result);
      }
    }
    //数据读取
    public function doMobileRead(){
      global $_W,$_GPC;
      $orderid=$_GPC['orderid'];
      $read=pdo_fetch("SELECT * FROM ".tablename('bu_order')."WHERE id=$orderid ");
      $pid=$read['pid'];
      $uid=$read['uid'];
      $pread=pdo_fetch("SELECT * FROM ".tablename('bu_address')."WHERE id=$pid and uniacid='".$_W['uniacid']."'");
      $uread=pdo_fetch("SELECT * FROM ".tablename('mc_members')."WHERE uid=$uid and uniacid='".$_W['uniacid']."'");
      if (!empty($uread)) {
        $result=array(
          'title'=>$pread['title'],
          'price'=>$read['price'],
          'aprice'=>$read['credit1'],
          'data'=>$pread,
          'status'=>1,
          'message'=>'succ'
        );
      }else {
        $result=array(
          'status'=>0,
          'message'=>'fail'
        );
      }
      echo json_encode($result);
    }
    // public function doMobilegps(){
    //   global $_W;
    //   if($_W['account']['level'] = ACCOUNT_SUBSCRIPTION_VERIFY) {
    //        	$infos = "积分兑换通知\n";
    //        	$infos.= "您在{$data['createtime']}进行对【{$name['title']}】商品积分兑换，使用{$setting}积分，积分余额【{$result_ticket}】积分。\n";
    //        	$message = array(
    //        		'msgtype' => 'text',
    //        		'text' => array('content' => urlencode($infos)),
    //        		'touser' =>opU630YFS_3xhYyNw_2Ep0njqh6I,
    //        	);
    //        	$account_api = WeAccount::create();
    //        	$status = $account_api->sendCustomNotice($message);
    //            if (is_error($status)) {
    //            	message('发送失败，原因为' . $status['message']);
    //            }
    //            //发送成功
    //
    //        }
    // }
    public function doMobileWechatBd(){
      global $_W,$_GPC;
      $userid = $_GPC['userid'];
      if(checkauth()){
        $uid = $_W['member']['uid'];
      }
      include $this->template('wechatbd');
    }

    // 更新答题操作
    public function doMobileUpdate(){
      global $_W,$_GPC;
      $userid = $_GPC['userid'];
      if(!empty($userid)){
        $data = array(
          'isQuestion' => 0
        );
        $res = pdo_update('bu_user',$data,array('id'=>$userid));
        if(!empty($res)){
          $return = array(
            'status' => 1,
            'message' => '更新成功'
          );
        }else{
          $return = array(
            'status' => 0,
            'message' => '您还没有答题'
          );
        }
      }else{
        $return = array(
          'status' => 0,
          'message' => '没有找到指定用户'
        );
      }
      echo json_encode($return);

    }


    // 短信发送
    public function sendSmsMb($phone,$params,$yzmType,$mbid){
      $Sms = new Sms();
      $status = $Sms::sendSms($phone,$params,$yzmType,$mbid);
      return $status;
    }

    // 数字转字符串
    public function numToString($num){
        $num = intval($num);
      	$numberToString = ['一','二','三','四','五','六','七','八','九','十'];
        if($num > 9){
          $stringFir = floor($num / 10);
          $stringSec = $num % 10;
          if($stringFir >= 2){
            $stringFir = $numberToString[$stringFir-1];
            $stringSec = $numberToString[$stringSec-1];
            $string = $stringFir.'十'.$stringSec;
          }else{
            $stringSec = $numberToString[$stringSec-1];
            $string = '十'.$stringSec;
          }
        }else{
          $string = $numberToString[$num-1];
        }
        return $string;
    }
    public function doMobileErtert(){
      $infoText =pdo_fetch('select * from'.tablename('bu_remind')."where uniacid = 245 and template_number = 'shengri'");
      //var_dump($infoText);

      $info = pdo_fetchall('SELECT a.`name`,a.`uid`,a.`identity`,b.`openid` FROM ' .tablename('bu_user').'a'." join ".tablename('mc_mapping_fans').'b' ." on a.uid = b.uid"." where a.uniacid = 245 and a.isauth = 2");
      //var_dump($info);
      foreach ($info as $key => $value) {
        $length = strlen($info[$key]['identity']);
        if ($length == '15') {
          $datetime=substr($info,8,4);
          $datetime=strtotime($datetime);
          $date=date('md',time());
          $dates=strtotime($date);
          if($datetime == $dates){
            if($_W['account']['level'] = ACCOUNT_SUBSCRIPTION_VERIFY) {
              $infos = $infoText['zdytext'];
              $message = array(
                'msgtype' => 'text',
                'text' => array('content' => urlencode($infos)),
                'touser' => $info[$key]['openid'],
              );
              $account_api = WeAccount::create();
              $status = $account_api->sendCustomNotice($message);
              if (is_error($status)) {
                message('发送失败，原因为' . $status['message']);
              }
            }
          }
        }else if($length == '18'){
          $datetime=substr($value['identity'],10,4);
          $datetime=strtotime($datetime);
          $date=date('md',time());
          $dates=strtotime($date);
          if($datetime == $dates){
            //var_dump($value);
            if($_W['account']['level'] = ACCOUNT_SUBSCRIPTION_VERIFY) {
              $infos = $infoText['zdytext'];
              $message = array(
                'msgtype' => 'text',
                'text' => array('content' => urlencode($infos)),
                'touser' => $info[$key]['openid'],
              );
              $account_api = WeAccount::create();
              $status = $account_api->sendCustomNotice($message);
              if (is_error($status)) {
                message('发送失败，原因为' . $status['message']);
              }
            }
          }
        }
      }
    }

}
