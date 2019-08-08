<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;
use think\facade\Session;

class Info extends Admin
{
    /**
     * 个人信息页面.
     */
    public function info()
    {
        $UserDB = model('User');
        $id = session('auth_id');
        if (!$id) {
            $this->error('参数错误!');
        }
        $role = model('Role')->getAllRole(array('status' => 1), 'sort DESC');
        $info = $UserDB->getUser(array('id' => $id));
        $this->assign('role', $role);
        $this->assign('info', $info);

        return $this->fetch();
    }

    /**
     * 修改密码
     */
    public function pass()
    {

        $UserDB = model('User');
        if ($this->request->isPost()) {
            $password = $_POST['password'];
            $repassword = $_POST['repassword'];
            if (!empty($password) || !empty($repassword)) {
                if ($password != $repassword) {
                    return msg_return('两次输入密码不一致！', 1);
                }
            }
            if (empty($password) && empty($repassword)) {
                return msg_return('密码必须填写', 1);
            }

            $userInfo = $UserDB->update($_POST);
            if (isset($userInfo[$userInfo->getPk()])) {
                Session::clear();

                return msg_return('编辑成功！');
            } else {
                $this->error($userInfo->getError());
            }
        } else {
            $id = session('auth_id');
            if (!$id) {
                $this->error('参数错误!');
            }
            $role = model('Role')->getAllRole(array('status' => 1), 'sort DESC');
            $info = $UserDB->getUser(array('id' => $id));
            $this->assign('role', $role);
            $this->assign('info', $info);

            return $this->fetch();
        }
    }
}
