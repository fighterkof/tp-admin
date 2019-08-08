<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;

class Test extends Admin
{
    public function index($map = '')
    {
        if ($this->request->param('title')) {
            $map['title'] = ['like', '%'.$this->request->param('title').'%'];
        }
        if ($this->request->param('pic')) {
            $map['pic'] = ['like', '%'.$this->request->param('pic').'%'];
        }

        $list = controller('Base', 'event')->commonlist('Test', $map);
        $this->assign('list', $list);

        return $this->fetch();
    }

    public function edit()
    {
        $list = controller('Base', 'event')->commonedit('Test');
        $this->assign('vo', $list);

        return $this->fetch();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $data = input('post.');
            $ret = controller('Base', 'event')->commonadd('Test', $data);
            if ($ret['code'] == 0) {
                return msg_return('发布成功！');
            } else {
                return msg_return($ret['data'], 1);
            }
        }

        return $this->fetch('edit');
    }
}
