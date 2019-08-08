<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;

class Sys extends Admin
{
    /**
     * 基本配置.
     */
    public function base($map = [])
    {
        $this->assign('sys', config('sys.'));
        $this->assign('ctrl', config('ctrl.'));
        // 配置信息保存
        if ($this->request->isPost()) {
            $config = input('post.');
            $url = \Env::get('module_path');
            foreach ($config as $k => $c) {
                $config_old = array();
                $config_new = array();
                switch ($k) {
                    case 'sys':
                        $config_old = require $url . '/config/sys.php';
                        if (is_array($c)) {
                            $config_new = array_merge($config_old, $c);
                        }
                        arr2file($url . '/config/sys.php', $config_new);
                        break;
                    case 'ctrl':
                        $config_old = require $url . '/config/ctrl.php';
                        if (is_array($c)) {
                            $config_new = array_merge($config_old, $c);
                        }
                        arr2file($url . '/config/ctrl.php', $config_new);
                        break;
                }
            }
            $this->success('更新成功！');
        }

        return $this->fetch();
    }

    /**
     * 消息配置..
     */
    public function msg()
    {
        $msg = config('msg.');
        $this->assign('msg', $msg);
        // 配置信息保存
        if ($this->request->isPost()) {
            $config = input('post.');
            $url = \Env::get('module_path');
            $config_old = array();
            $config_new = array();
            $config_old = require $url . '/config/msg.php';
            if (is_array($config)) {
                $config_new = array_merge($config_old, $config);
            }
            arr2file($url . '/config/msg.php', $config_new);
            $this->success('更新成功！');
        }

        return $this->fetch();
    }

    /**
     * 系统日志.
     */
    public function logs()
    {
        $list = controller('Base', 'event')->commonlist('sys_logs');
        $this->assign('list', $list);

        return $this->fetch();
    }
}
