<?php

namespace app\hshadmin\controller;

use think\Controller;
use think\Db;
use think\Exception;
use think\facade\App;
use think\facade\Config;
use think\facade\Session;

class Login extends Controller
{
    public function login()
    {
        return $this->fetch();
    }

    /**
     * 登录检测.
     *
     * @return \think\response\Json
     */
    public function checkLogin()
    {
        $data = $this->request->post();
        if ($this->request->isPost()) {
            $captcha = input('captcha');
            if (!captcha_check($captcha)) {
                return msg_return('验证码错误！', 1);
            }
            $map['username'] = $data['username'];
            $map['status'] = 1;
            $auth_info = \Rbac::authenticate($map);
            // 使用用户名、密码和状态的方式进行认证

            if (null === $auth_info) {
                return msg_return('帐号不存在或已禁用！', 1);
            } else {
                if ($auth_info['password'] != md5(md5(sha1($data['password'])))) {
                    return msg_return('密码错误！', 1);
                }
                // 生成session信息
                Session::set(Config::get('rbac.user_auth_key'), $auth_info['id']);
                Session::set('user_name', $auth_info['username']);
                Session::set('roleid', $auth_info['role']);
                Session::set('real_name', $auth_info['remark']);
                Session::set('last_login_ip', $auth_info['last_login_ip']);
                Session::set('last_login_time', $auth_info['last_login_time']);
                // 超级管理员标记
                if ($auth_info['id'] == 1) {
                    Session::set(Config::get('rbac.admin_auth_key'), true);
                }
                // 记录登录日志
                $log['uid'] = $auth_info['id'];
                $log['login_ip'] = $this->request->ip();
                $log['login_location'] = implode(' ', \Ip::find($log['login_ip']));
                $log['login_browser'] = \Agent::getBroswer();
                $log['login_os'] = \Agent::getOs();
                $log['login_time'] = time();
                Db::name('userlog')->insert($log);
                // 缓存访问权限
                \Rbac::saveAccessList();

                return msg_return('登入成功！');
                exit;
            }
        } else {
            throw new Exception('非法请求');
        }
    }

    /**
     * 用户登出.
     */
    public function logout()
    {
        defined('UID') or define('UID', Session::get(Config::get('rbac.user_auth_key')));
        if (UID) {
            Session::clear();
            $this->success('登出成功！', Config::get('rbac.user_auth_gateway'));
        } else {
            $this->error('已经登出！', Config::get('rbac.user_auth_gateway'));
        }
    }
}
