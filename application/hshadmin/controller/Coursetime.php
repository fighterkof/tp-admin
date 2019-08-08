<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;
use think\facade\App;

class Coursetime extends Admin
{

    public function index()
    {
        $pagenumber = config('ctrl.pagenum'); //获取没页的页数量
        $keyword = input('keyword');
        $map = [];
        if ($keyword) {
            $map[] = ['title', 'like', '%' . $keyword . '%'];
        }

        $list = db('course_time')->where($map)->order(['displayorder' => 'desc', 'id' => 'desc'])->paginate($pagenumber, false, ['query' => request()->param()]);

        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);

        return $this->fetch();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $data = input('post.');
            $data['createtime'] = time();
            //$data['ad_app_link'] = input('post.ad_app_link', 0, 'intval');
            $info = db('course_time')->insert($data);
            if ($info) {
                return msg_return('添加成功！');
            }
        }

        return $this->fetch();
    }

    public function edit()
    {
        $id = input('id', 0, 'intval');
        if (!$id) {
            return msg_return('参数错误！', 1);
        }

        if ($this->request->isPost()) {
            $data = input('post.');
            //$data['ad_app_link'] = input('post.ad_app_link', 0, 'intval');
            db('course_time')->where(['id' => $id])->update($data);

            return msg_return('修改成功！');
        }

        $info = db('course_time')->where(['id' => $id])->find();

        $this->assign('info', $info);

        return $this->fetch('add');
    }

    public function delete()
    {
        $id = input('get.id');
        if (!$id) {
            return msg_return('参数错误！', 1);
        }

        db('course_time')->where('id', $id)->delete();

        return msg_return('删除成功！');
    }
}
