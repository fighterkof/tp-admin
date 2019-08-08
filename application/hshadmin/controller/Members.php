<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;
use think\facade\App;

class Members extends Admin
{
    //用户列表
    public function index()
    {
        $pagenumber = config('ctrl.pagenum'); //获取没页的页数量
        $keyword = input('keyword');
        $map = [];
        if ($keyword) {
            $map[] = ['phone', 'like', '%' . $keyword . '%'];
        }

        $list = db('members')->where($map)->order(['status' => 'asc', 'status_teacher' => 'asc', 'members_id' => 'desc'])->paginate($pagenumber, false, ['query' => request()->param()]);

        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    //删除用户、
    public function delete()
    {
        $members_id = input('members_id', 0, 'intval');
        if (!$members_id) {
            return msg_return('参数错误！', 1);
        }
        db('members')->where('members_id', $members_id)->delete();

        return msg_return('删除成功！');
    }

    //异步设置成为家长
    public function ajaxSet()
    {
        $members_id = input('members_id', 0, 'intval');
        if (!$members_id) {
            return msg_return('参数错误！', 1);
        }
        $update_members = db('members')->where(['members_id' => $members_id])->update(['status' => 1]);
        if ($update_members) {
            $members = db('members')->where(['members_id' => $members_id])->find();
            $data = array(
                'parent_name' => $members['user_name'],
                'phone' => $members['phone'],
                'openid' => $members['openid'],
                'createtime' => time(),
            );
            $insert_parent = db('parent')->insert($data);
            if ($insert_parent) {
                return json(['msg' => '设置成功', 'code' => 0]);
            }
        }
    }

    //异步设置成为老师
    public function ajaxTeacher()
    {
        $members_id = input('members_id', 0, 'intval');
        if (!$members_id) {
            return msg_return('参数错误！', 1);
        }
        $update_members = db('members')->where(['members_id' => $members_id])->update(['status_teacher' => 1]);
        if ($update_members) {
            $members = db('members')->where(['members_id' => $members_id])->find();
            $data = array(
                'teacher_name' => $members['user_name'],
                'phone' => $members['phone'],
                'openid' => $members['openid'],
                'createtime' => time(),
            );
            $insert_parent = db('teacher')->insert($data);
            if ($insert_parent) {
                return json(['msg' => '设置成功', 'code' => 0]);
            }
        }
    }

    //家长列表
    public function parent()
    {
        $pagenumber = config('ctrl.pagenum'); //获取没页的页数量
        $keyword = input('keyword');
        $map = [];
        if ($keyword) {
            $map[] = ['phone', 'like', '%' . $keyword . '%'];
        }
        $list = db('parent')->where($map)->order(['parent_id' => 'desc'])->paginate($pagenumber, false, ['query' => request()->param()])->each(function ($item, $key) {
            $student = db('student')->where(['parent_id' => $item['parent_id']])->select();
            $item['student'] = $student;
            return $item;
        });
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    //删除家长
    public function parent_del()
    {
        $parent_id = input('parent_id', 0, 'intval');
        if (!$parent_id) {
            return msg_return('参数错误！', 1);
        }
        db('parent')->where('parent_id', $parent_id)->delete();

        return msg_return('删除成功！');
    }

    //添加学员
    public function studentadd()
    {
        $parent_id = input('parent_id', 0, 'intval');
        $this->assign('parent_id', $parent_id);
        if ($this->request->isPost()) {
            $data = input('post.');
            $info = db('student')->insert($data);
            if ($info) {
                return msg_return('添加成功！');
            }
        }

        return $this->fetch();
    }
}
