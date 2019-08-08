<?php

//------------------------
// 自动生成代码
//-------------------------

namespace app\hshadmin\controller;

use app\common\controller\admin;
use think\Controller;
use think\Db;
use tpdf\tpdf;

class Fd extends Admin
{
    public function initialize()
    {
        parent::initialize();
        $this->tpdf = new tpdf();
        $this->uid = session('uid');
        $this->role = session('role');
    }

    /**
     * 首页.
     *
     * @return mixed
     */
    public function index($map = [])
    {
        if ($this->request->param('title')) {
            $map[] = ['title', 'like', '%'.$this->request->param('title').'%'];
        }
        $list = controller('Base', 'event')->commonlist('fd', $map);
        $this->assign('list', $list);

        return $this->view->fetch();
    }

    /**
     * 表单设计
     *
     * @return mixed
     */
    public function desc($map = [])
    {
        if ($this->request->isPost()) {
            $data = input('post.');
            $data['ziduan'] = htmlspecialchars_decode($data['ziduan']);
            $ret = $this->tpdf->FbApi('Save', $data['id'], $data);
            if ($ret['status'] == 1) {
                return msg_return('修改成功！');
            } else {
                return msg_return($ret['data'], 1);
            }
        }
        $info = $this->tpdf->FbApi('Desc', input('id'));
        $this->assign('ziduan', $info['field']);
        $this->assign('fid', $info['fid']);

        return $this->view->fetch();
    }

    /**
     * 表单设计修改.
     *
     * @return mixed
     */
    public function edit_desc()
    {
        $info = $this->tpdf->FbApi('Edit', input('id'));
        $this->assign('info', $info['info']);
        $this->assign('ziduan', $info['info']['field']);
        $this->assign('fid', input('id'));

        return $this->view->fetch('desc');
    }

    /**
     * 设计预览.
     *
     * @return mixed
     */
    public function dsec_view()
    {
        $this->tpdf->make($this->tpdf->FbApi('Bview', input('id')), 'demo');

        return $this->view->fetch('demo/view');
    }

    /**
     * 首页.
     *
     * @return mixed
     */
    public function add($map = [])
    {
        if ($this->request->isPost()) {
            $data = input('post.');
            $ret = controller('Base', 'event')->commonadd('fd', $data);
            if ($ret['code'] == 0) {
                return msg_return('发布成功！');
            } else {
                return msg_return($ret['data'], 1);
            }
        }
        $this->assign('top', db('node')->where('status', 1)->where('pid', 1)->where('level', 0)->where('display', 1)->select());

        return $this->view->fetch();
    }

    public function functions()
    {
        if ($this->request->isPost()) {
            $data = input('post.');
            $ret = controller('Base', 'event')->commonadd('fd_fun', $data);
            if ($ret['code'] == 0) {
                return msg_return('发布成功！');
            } else {
                return msg_return($ret['data'], 1);
            }
        }
        $this->assign('fid', input('id'));

        return $this->view->fetch();
    }

    public function ajax_sql()
    {
        if ($this->request->isPost()) {
            $sql = input('post.sql');
            $title = input('post.title');
            try {
                $data = Db::query($sql);
                $html = '<select><option value=>请选择'.$title.'</option>';
                foreach ($data as $k => $v) {
                    $html .= '<option value="'.$v['id'].'">'.$v['name'].'</option>';
                }
                $html .= '</select>';
                echo  $html;
            } catch (\Exception $e) {
                return  1;
            }
        }
    }

    public function shengcheng()
    {
        $data = $this->tpdf->FbApi('Bview', input('id'), 'Act');
        $this->tpdf->make($data);
        $this->success('生成成功！', '/index/index/welcome');
    }

    public function BulidNode($id)
    {
        $info = db('fd')->find($id);

        $Test_controller = ucfirst($info['name']);
        $node_top = ['name' => $Test_controller, 'status' => 1, 'title' => $info['title'], 'pid' => $info['node'], 'level' => 2, 'display' => 2];
        $top_id = db('node')->insertGetId($node_top);
        $node_index = ['name' => 'index', 'data' => $Test_controller.'/index', 'status' => 1, 'title' => $info['title'], 'pid' => $top_id, 'level' => 2, 'display' => 2];
        $index_id = db('node')->insertGetId($node_index);
        $node_data = [
            ['name' => 'index', 'status' => 1, 'title' => '列表', 'data' => 'add', 'pid' => $index_id, 'level' => 3, 'display' => 0],
            ['name' => 'add', 'status' => 1, 'title' => '新增', 'data' => 'add', 'pid' => $index_id, 'level' => 3, 'display' => 0],
            ['name' => 'edit', 'status' => 1, 'title' => '修改', 'data' => 'edit', 'pid' => $index_id, 'level' => 3, 'display' => 0],
        ];
        $ids = db('node')->insertAll($node_data);
        controller('Base', 'event')->commonedit('fd', ['id' => input('id'), 'status' => 1]);
        $this->success('生成成功！', '/index/index/welcome');
    }
}
