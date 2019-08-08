<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;
use function GuzzleHttp\json_encode;
use think\facade\App;

class Notice extends Admin
{
    //公告列表
    public function index()
    {
        $pagenumber = config('ctrl.pagenum'); //获取没页的页数量
        $keyword = input('keyword');
        $map = [];
        if ($keyword) {
            $map[] = ['title', 'like', '%' . $keyword . '%'];
        }

        $list = db('notice')->where($map)->order(['status' => 'desc', 'notice_id' => 'desc'])->paginate($pagenumber, false, ['query' => request()->param()]);

        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    //添加公告
    public function add()
    {
        if ($this->request->isPost()) {
            $data = input('post.');
            $data['show_time'] = strtotime(input('post.show_time'));
            $data['createtime'] = time();
            //$data['ad_app_link'] = input('post.ad_app_link', 0, 'intval');
            $info = db('notice')->insert($data);
            if ($info) {
                return msg_return('添加成功！');
            }
        }
        return $this->fetch();
    }

    //修改公告
    public function edit()
    {
        $notice_id = input('notice_id', 0, 'intval');
        if (!$notice_id) {
            return msg_return('参数错误！', 1);
        }

        if ($this->request->isPost()) {
            $data = input('post.');
            $data['show_time'] = strtotime(input('post.show_time'));
            //$data['ad_app_link'] = input('post.ad_app_link', 0, 'intval');
            $info = db('notice')->where(['notice_id' => $notice_id])->update($data);
            if ($info) {
                return msg_return('修改成功！');
            }
        }

        $info = db('notice')->where(['notice_id' => $notice_id])->find();
        $info['show_time'] = date('Y-m-d', $info['show_time']);
        $this->assign('info', $info);
        return $this->fetch('add');
    }

    //删除公告
    public function delete()
    {
        $notice_id = input('notice_id', 0, 'intval');
        if (!$notice_id) {
            return msg_return('参数错误！', 1);
        }
        db('notice')->where('notice_id', $notice_id)->delete();

        return msg_return('删除成功！');
    }

    //监听推荐数是否达到8
    public function ajaxRecommend()
    {
        $notice = db('notice')->where(['status' => 1])->count();
        if ($notice >= 8) {
            $arr = array(
                'code' => 0,
                'msg' => '你所推荐的公告已有8条，无需再次推荐',
            );
        }
        echo json_encode($arr);
    }
}
