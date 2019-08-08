<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;
use think\facade\App;

class Franchise extends Admin
{
    /**
     * 加盟列表
     */
    public function index()
    {
        $pagenumber = config('ctrl.pagenum'); //获取没页的页数量
        $keyword = input('keyword');
        $type = input('type', '0');
        $map = [];
        if ($keyword) {
            $map[] = ['phone', 'like', '%' . $keyword . '%'];
        }

        $list = db('web_franchise')->where($map)->order(['status' => 'asc', 'franchise_id' => 'desc'])->paginate($pagenumber, false, ['query' => request()->param()]);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }
    /**
     * 加盟列表查看
     */
    public function see()
    {
        $franchise_id = input('franchise_id', 0, 'intval');
        if (!$franchise_id) {
            return msg_return('参数错误');
        }
        $info = db('web_franchise')->where('franchise_id', $franchise_id)->find();
        $this->assign('info', $info);
        return $this->fetch();
    }

    /**
     * 加盟列表修改状态
     */
    public function update_status()
    {
        $franchise_id = input("get.franchise_id");
        if (!$franchise_id) {
            return msg_return('参数错误！', 1);
        }
        $franchise = db('web_franchise')->where(['franchise_id' => $franchise_id])->find();
        if ($franchise['status'] == 1) {
            $status = 0;
            db('web_franchise')->where('franchise_id', $franchise_id)->update(['status' => 0]);
        } else {
            $status = 1;
            db('web_franchise')->where('franchise_id', $franchise_id)->update(['status' => 1]);
        }

        return msg_return('修改成功！', 0, $status);
    }

    /**
     * 加盟列表删除
     */
    public function delete()
    {
        $franchise_id = input("get.franchise_id");
        if (!$franchise_id) {
            return msg_return('参数错误！', 1);
        }

        db('web_franchise')->where('franchise_id', $franchise_id)->delete();

        return msg_return('删除成功！');
    }
    /**
     * 加盟流程列表
     */
    public function process()
    {
        $list = db('web_franchise_process')->select();
        $this->assign('list', $list);
        return $this->fetch();
    }
    /**
     * 加盟流程修改
     */
    public function editprocess()
    {
        $process_id = input('process_id', 0, 'intval');
        if (!$process_id) {
            return msg_return('参数错误！', 1);
        }
        if ($this->request->isPost()) {
            $data = input('post.');
            db('web_franchise_process')->where(['process_id' => $process_id])->update($data);

            return msg_return('修改成功！');
        }
        $info = db('web_franchise_process')->where('process_id', $process_id)->find();
        $this->assign('info', $info);
        return $this->fetch();
    }

    /**
     * 运营支持
     */
    public function operate()
    {
        $pagenumber = config('ctrl.pagenum'); //获取没页的页数量
        $list = db('web_image')->where(['status' => 1, 'topic_type' => 3])->order(['displayorder' => 'asc'])->paginate($pagenumber, false, ['query' => request()->param()]);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    /**
     * 添加运营支持
     */
    public function addoperate()
    {
        if ($this->request->isPost()) {
            $data = input('post.');
            $data['createtime'] = time();
            $data['topic_type'] = 3;
            if (!$data['image']) {
                return msg_return('上传图像', 1);
            }

            $info = db('web_image')->insert($data);
            if ($info) {
                return msg_return('添加成功！');
            }
        }
        return $this->fetch();
    }

    /**
     * 修改运营支持
     */
    public function editoperate()
    {
        $id = input('id', 0, 'intval');
        if (!$id) {
            return msg_return('参数错误！', 1);
        }
        if ($this->request->isPost()) {
            $data = input('post.');

            db('web_image')->where(['id' => $id])->update($data);

            return msg_return('修改成功！');
        }
        $info = db('web_image')->where('id', $id)->find();
        $this->assign('info', $info);
        return $this->fetch('addoperate');
    }

    /**
     * 删除运营支持
     */
    public function deloperate()
    {
        $id = input("get.id");
        if (!$id) {
            return msg_return('参数错误！', 1);
        }

        db('web_image')->where('id', $id)->delete();

        return msg_return('删除成功！');
    }
}
