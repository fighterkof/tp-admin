<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;
use think\facade\App;

class News extends Admin
{
    /**
     *用户列表.
     */
    public function index($map = [])
    {
        /*
        if ($this->request->param('username')) {
            $map[] = ['username', 'like', '%'.$this->request->param('username').'%'];
        }
        $list = controller('Base', 'event')->commonlist('User', $map);
        $this->assign('list', $list);*/
        $list = array();
        $this->assign('list', $list);

        return $this->fetch();
    }

    /**
     *新增用户.
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     *用户编辑.
     */
    public function edit()
    {
    }

    /**
     *用户删除.
     */
    public function del()
    {
    }
}
