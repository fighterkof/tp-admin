<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;
use think\facade\App;

class Banner extends Admin
{
    protected function getPositionName($id)
    {
        // return '11';
    }

    public function index()
    {
        $pagenumber = config('ctrl.pagenum'); //获取没页的页数量
        $keyword = input('keyword');
        $map = [];
        if ($keyword) {
            $map[] = ['ad_name', 'like', '%' . $keyword . '%'];
        }

        $aaaa='bbb';
        $bbbb='ttt';
        $list = db('banner')->where($map)->order(['displayorder' => 'desc', 'banner_id' => 'desc'])->paginate($pagenumber, false, ['query' => request()->param()]);

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
            $info = db('Banner')->insert($data);
            if ($info) {
                return msg_return('添加成功！');
            }
        }
        $app_path = db('app_ad_path')->where(['type' => 1])->order(['id' => 'desc'])->select();
        $this->assign('app_path', $app_path);

        $mini_path = db('app_ad_path')->where(['type' => 2])->order(['id' => 'desc'])->select();
        $this->assign('mini_path', $mini_path);

        return $this->fetch();
    }

    public function edit()
    {
        $id = input('banner_id', 0, 'intval');
        if (!$id) {
            return msg_return('参数错误！', 1);
        }

        if ($this->request->isPost()) {
            $data = input('post.');
            //$data['ad_app_link'] = input('post.ad_app_link', 0, 'intval');
            db('banner')->where(['banner_id' => $id])->update($data);

            return msg_return('修改成功！');
        }

        $info = db('banner')->where(['banner_id' => $id])->find();
        if ($info['start_time'] == '0000-00-00' && $info['end_time'] == '0000-00-00') {
            $info['start_time'] = '';
            $info['end_time'] = '';
        }
        $this->assign('info', $info);

        $mini_path = db('app_ad_path')->where(['type' => 2])->order(['id' => 'desc'])->select();
        $this->assign('mini_path', $mini_path);

        $app_path = db('app_ad_path')->order(['id' => 'desc'])->select();
        $this->assign('app_path', $app_path);

        return $this->fetch('add');
    }

    public function delete()
    {
        $id = input('get.banner_id');
        if (!$id) {
            return msg_return('参数错误！', 1);
        }

        db('banner')->where('banner_id', $id)->delete();

        return msg_return('删除成功！');
    }
}
