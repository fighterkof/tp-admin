<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Exception;
use think\facade\App;
use think\facade\Config;
use think\facade\Session;
use think\Response;

class Index extends Controller
{
   public function index(){
    $member_id=input('member_id');
    $this->assign('member_id', $member_id);

    $to_id=input('to_id');
    $this->assign('to_id', $to_id);
    return $this->fetch();
   }

   public function ajax(){

    if ($this->request->isPost()) {
        $data = input('post.');
        $data['createtime'] = time();
       

        $info = db('message')->insert($data);
        if ($info) {

            $datas=[
                'name'=>input('name'),
                'content'=>input('content')
            ];
            return $this->msg_return('添加成功！',0,$datas);
        }
    }
   }


   function msg_return($msg = '操作成功！', $code = 0, $data = [], $redirect = 'parent', $alert = '', $close = false, $url = '')
{
    $ret = ['code' => $code, 'msg' => $msg, 'data' => $data];
    $extend['opt'] = [
        'alert' => $alert,
        'close' => $close,
        'redirect' => $redirect,
        'url' => $url,
    ];
    $ret = array_merge($ret, $extend);

    return Response::create($ret, 'json');
}


}


