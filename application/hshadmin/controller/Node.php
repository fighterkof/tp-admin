<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;
use Tree;

class Node extends Admin
{
    /**
     *节点列表.
     */
    public function index()
    {
        $Node = obj2arr(model('Node')->getAllNode());
        $array = array();
        // 构建生成树中所需的数据
        foreach ($Node as $k => $r) {
            $r['id'] = $r['id'];
            $r['title'] = $r['title'];
            $r['name'] = $r['name'];
            $r['status'] = $r['status'] == 1 ? '<font color="red">√</font>' : '<font color="blue">×</font>';
            $r['submenu'] = $r['level'] == 3 ? '<font color="#cccccc">添加子菜单</font>' : "<a href='javascript:void(0)' onclick='xadmin.open(\"菜单添加\",\"" . url('/hshadmin/Node/add/pid/' . $r['id']) . "\")'>添加子菜单</a>";
            $r['edit'] = $r['level'] == 1 ? '<font color="#cccccc">修改</font>' : "<a href='javascript:void(0)' onclick='xadmin.open(\"菜单添加\",\"" . url('/hshadmin/Node/edit/id/' . $r['id'] . '/pid/' . $r['pid']) . "\")'>修改</a>";
            $r['del'] = $r['level'] == 1 ? '<font color="#cccccc">删除</font>' : "<a onclick='admin_del(this,\"" . $r['id'] . "\")'  href='javascript:void(0)'>删除</a>";

            $r['pid_node'] = ($r['pid']) ? ' class=" child-of-node-' . $r['pid'] . '"' : '';

            switch ($r['display']) {
                case 0:
                    $r['display'] = '不显示';
                    break;
                case 1:
                    $r['display'] = '主菜单';
                    break;
                case 2:
                    $r['display'] = '子菜单';
                    break;
            }
            switch ($r['level']) {
                case 0:
                    $r['level'] = '非节点';
                    break;
                case 1:
                    $r['level'] = '应用';
                    break;
                case 2:
                    $r['level'] = '模块';
                    break;
                case 3:
                    $r['level'] = '方法';
                    break;
            }
            $array[] = $r;
        }

        $str = "<tr  id='node-\$id' \$pid_node>
				    <td align='center'><input type='text'class='layui-input' style='width:50%;display:inline-block' value='\$sort' size='2' name='sort[\$id]'></td>
				    <td align='center'>\$id</td>
				    <td class='text-l'>\$spacer \$title (\$name)</td>
				    <td align='center'>\$level</td>
				    <td align='center'>\$status</td>
				    <td align='center'>\$display</td>
					<td align='center'>
						\$submenu | \$edit | \$del
					</td>
				  </tr>";

        $Tree = new Tree();
        $Tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $Tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $Tree->init($array);
        $html_tree = $Tree->get_tree(0, $str);
        $this->assign('html_tree', $html_tree);
        $this->assign('aaa', '<b>123132</b>');

        return $this->fetch();
    }

    /**
     *添加节点.
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $NodeDB = model('Node');
            //根据表单提交的POST数据创建数据对象
            $nodeInfo = $NodeDB->create($this->request->post());
            if (isset($nodeInfo[$nodeInfo->getPk()])) {
                return msg_return('操作成功！');
            } else {
                $this->error($nodeInfo->getError());
            }
            exit();
        } else {
            $Node = obj2arr(model('Node')->getAllNode());
            $pid = input('pid', '0', 'intval'); //选择子菜单
            $array = array();
            foreach ($Node as $k => $r) {
                $r['id'] = $r['id'];
                $r['title'] = $r['title'];
                $r['name'] = $r['name'];
                $r['disabled'] = $r['level'] == 3 ? 'disabled' : '';
                $array[$r['id']] = $r;
            }
            $str = "<option value='\$id' \$selected \$disabled >\$spacer \$title</option>";
            $Tree = new Tree();
            $Tree->init($array);
            $select_categorys = $Tree->get_tree(0, $str, $pid);
            $this->assign('tpltitle', '添加');
            $this->assign('select_categorys', $select_categorys);

            return $this->fetch();
        }
    }

    /**
     *编辑节点.
     */
    public function edit()
    {
        $NodeDB = model('Node');
        if ($this->request->isPost()) {
            $nodeInfo = $NodeDB->update($this->request->post());
            if (isset($nodeInfo[$nodeInfo->getPk()])) {
                return msg_return('操作成功！');
            } else {
                $this->error($nodeInfo->getError());
            }
        } else {
            $id = input('id', '0', 'intval');
            $pid = input('pid', '0', 'intval'); //选择子菜单
            if (!$id || !$pid) {
                $this->error('参数错误!');
            }
            $allNode = obj2arr($NodeDB->getAllNode());
            $array = array();
            foreach ($allNode as $k => $r) {
                $r['id'] = $r['id'];
                $r['title'] = $r['title'];
                $r['name'] = $r['name'];
                $r['disabled'] = $r['level'] == 3 ? 'disabled' : '';
                $array[$r['id']] = $r;
            }
            $str = "<option value='\$id' \$selected \$disabled >\$spacer \$title</option>";
            $Tree = new Tree();
            $Tree->init($array);
            $select_categorys = $Tree->get_tree(0, $str, $pid);
            $this->assign('tpltitle', '编辑');
            $this->assign('select_categorys', $select_categorys);
            $this->assign('info', $NodeDB->getNode('id=' . $id));

            return $this->fetch('add');
        }
    }

    /**
     *删除节点.
     */
    public function del()
    {
        $id = input('id', '0', 'intval');
        if (!$id) {
            $this->error('参数错误!');
        }
        $NodeDB = model('Node');
        if (!$NodeDB->childNode($id)) {
            return json(['msg' => '存在子菜单，不可删除!', 'code' => 1]);
        }
        if ($NodeDB->delNode('id=' . $id)) {
            return json(['msg' => '删除成功！!', 'code' => 0]);
        } else {
            $this->error('删除失败!');

            return json(['msg' => '删除失败!', 'code' => 1]);
        }
    }

    /**
     *排序更新.
     */
    public function sort()
    {
        $sorts = $_POST['sort'];
        if (!is_array($sorts)) {
            $this->error('参数错误!');
        }
        foreach ($sorts as $id => $sort) {
            model('Node')->upNode(array('id' => $id, 'sort' => intval($sort)));
        }
        $this->success('更新完成！', url('/hshadmin/Node/index'));
    }
}
