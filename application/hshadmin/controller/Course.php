<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;
use think\facade\App;

class Course extends Admin
{
    //课程列表
    public function index()
    {
        $list = db('course')->where(['parent_id' => 0])->select();
        foreach ($list as $k => $v) {
            $list[$k]['son'] = db('course')->where(['parent_id' => $v['course_id']])->select();
            foreach ($list[$k]['son'] as $key => $val) {
                $list[$k]['son'][$key]['son_xia'] = db('course')->where(['parent_id' => $val['course_id']])->select();
            }
        }
        $this->assign('list', $list);
        return $this->fetch();
    }

    //添加课程
    public function add()
    {
        $course_id = input('course_id', 0, 'intval');
        $this->assign('course_id', $course_id);
        $course = db('course')->where(['course_id' => $course_id])->find();
        $this->assign('info', $course);
        if ($this->request->isPost()) {
            $course_id = input('post.parent_id');
            $course = db('course')->where(['course_id' => $course_id])->find();
            $data = input('post.');
            $data['createtime'] = time();
            if ($course['hierarchy'] == 1) {
                $data['hierarchy'] = 2;
            } else if ($course['hierarchy'] == 2) {
                $data['hierarchy'] = 3;
            } else if (!$course) {
                $data['hierarchy'] = 1;
            }
            $info = db('course')->insert($data);
            if ($info) {
                return msg_return('添加成功！');
            }
        }
        return $this->fetch();
    }

    //修改课程
    public function edit()
    {
        $course_id = input('course_id', 0, 'intval');
        if (!$course_id) {
            return msg_return('参数错误！', 1);
        }

        if ($this->request->isPost()) {
            $data = input('post.');
            $info = db('course')->where(['course_id' => $course_id])->update($data);
            if ($info) {
                return msg_return('修改成功！');
            }
        }

        $info = db('course')->where(['course_id' => $course_id])->find();
        // dump($info);exit;
        $this->assign('info', $info);
        return $this->fetch('add');
    }

    //删除公告
    public function delete()
    {
        $course_id = input('course_id', 0, 'intval');
        if (!$course_id) {
            return msg_return('参数错误！', 1);
        }
        $course = db('course')->where(['parent_id' => $course_id])->select();
        // dump($course);exit;
        if ($course) {
            return msg_return('该课程下有子课程，请先删除子课程！', 1);
        }

        db('course')->where('course_id', $course_id)->delete();

        return msg_return('删除成功！');
    }
}
