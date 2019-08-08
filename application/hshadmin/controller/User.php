<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;
use think\Db;
use Tree;

class User extends Admin
{
    /**
     *用户列表.
     */
    public function index($map = [])
    {
        if ($this->request->param('username')) {
            $map[] = ['username', 'like', '%' . $this->request->param('username') . '%'];
        }
        $list = controller('Base', 'event')->commonlist('User', $map);
        $this->assign('list', $list);

        return $this->fetch();
    }

    /**
     *新增用户.
     */
    public function add()
    {
        $UserDB = model('User');
        if ($this->request->isPost()) {
            $password = $_POST['password'];
            $repassword = $_POST['repassword'];
            if (empty($password) || empty($repassword)) {
                return msg_return('密码必须！', 1);
            }
            if ($password != $repassword) {
                return msg_return('两次输入密码不一致！', 1);
            }
            //根据表单提交的POST数据创建数据对象
            $userInfo = $UserDB->create($_POST);
            if (isset($userInfo[$userInfo->getPk()])) {
                $data['user_id'] = $userInfo->id;
                $data['role_id'] = input('post.role');
                if (model('RoleUser')->addRoleUser($data)) {
                    return msg_return('添加成功！');
                } else {
                    return msg_return('用户添加成功,但角色对应关系添加失败', 1);
                }
            } else {
                $this->error($userInfo->getError());
            }
        } else {
            $role = model('Role')->getAllRole(array('status' => 1), 'sort DESC');
            $this->assign('role', $role);

            return $this->fetch();
        }
    }

    /**
     *用户编辑.
     */
    public function edit()
    {
        $UserDB = model('User');
        if ($this->request->isPost()) {
            $password = $_POST['password'];
            $repassword = $_POST['repassword'];
            if (!empty($password) || !empty($repassword)) {
                if ($password != $repassword) {
                    return msg_return('两次输入密码不一致！', 1);
                }
            }
            if (empty($password) && empty($repassword)) {
                unset($_POST['password']);
            } //不填写密码不修改
            $userInfo = $UserDB->update($_POST);
            //根据表单提交的POST数据创建数据对象
            if (isset($userInfo[$userInfo->getPk()])) {
                $where['user_id'] = $this->request->post('id');
                $data['role_id'] = $this->request->post('role');
                model('RoleUser')->upRoleUser($where, $data);

                return msg_return('编辑成功！');
            } else {
                $this->error($userInfo->getError());
            }
        } else {
            $id = input('param.id', 0, 'intval');
            if (!$id) {
                $this->error('参数错误!');
            }
            $role = model('Role')->getAllRole(array('status' => 1), 'sort DESC');
            $info = $UserDB->getUser(array('id' => $id));
            $this->assign('role', $role);
            $this->assign('info', $info);

            return $this->fetch('add');
        }
    }

    /**
     *用户验证
     */
    public function check_username()
    {
        $userid = $this->request->param('userid', '0', 'intval');
        $username = $this->request->param('username');
        if (model('User')->check_name($username, $userid)) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     *用户修改.
     */
    public function change()
    {
        $id = input('param.id', 0, 'intval');
        if (!$id) {
            $this->error('参数错误!');
        }
        $UserDB = model('User');
        $data['status'] = input('param.status');
        $data['id'] = $id;
        if ($UserDB->update($data)) {
            return json(['msg' => '操作成功！', 'code' => 0]);
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     *用户删除.
     */
    public function del()
    {
        $id = input('param.id', 0, 'intval');
        if (!$id) {
            $this->error('参数错误!');
        }
        $UserDB = model('User');
        $info = $UserDB->getUser(array('id' => $id));
        if ($info['username'] == config('SPECIAL_USER')) { //无视系统权限的那个用户不能删除
            $this->error('禁止删除此用户!');
        }

        if ($UserDB->delUser('id=' . $id)) {
            if (model('RoleUser')->where('user_id=' . $id)->delete()) {
                return json(['msg' => '删除成功！', 'code' => 0]);
            } else {
                return json(['msg' => '用户成功,但角色对应关系删除失败!', 'code' => 1]);
            }
        } else {
            $this->error('删除失败!');
        }
    }

    /* ========角色部分======== */

    /**
     *角色列表.
     */
    public function role()
    {
        $list = controller('Base', 'event')->commonlist('Role');
        $this->assign('list', $list);

        return $this->fetch();
    }

    /**
     *添加角色.
     */
    public function addrole()
    {
        $RoleDB = model('Role');
        if ($this->request->isPost()) {
            //根据表单提交的POST数据创建数据对象
            $roleInfo = $RoleDB->create(input('post.'));

            $id = $roleInfo[$roleInfo->getPk()];

            if (isset($roleInfo[$roleInfo->getPk()])) {
                $data = [
                    ['role_id' => $id, 'node_id' => '181', 'pid' => '64', 'level' => '2', 'module' => ''],
                    ['role_id' => $id, 'node_id' => '182', 'pid' => '64', 'level' => '2', 'module' => ''],
                    ['role_id' => $id, 'node_id' => '64', 'pid' => '14', 'level' => '2', 'module' => ''],
                    ['role_id' => $id, 'node_id' => '14', 'pid' => '1', 'level' => '0', 'module' => ''],
                    ['role_id' => $id, 'node_id' => '1', 'pid' => '0', 'level' => '1', 'module' => ''],
                ];
                Db::name('access')->insertAll($data);

                return msg_return('添加成功！');
            } else {
                return msg_return($roleInfo->getError(), 1);
            }
        } else {
            return $this->fetch();
        }
    }

    /**
     *编辑角色.
     */
    public function roleedit()
    {
        $RoleDB = model('Role');
        if ($this->request->isPost()) {
            //根据表单提交的POST数据创建数据对象
            $roleInfo = $RoleDB->update(input('post.'));

            if (isset($roleInfo[$roleInfo->getPk()])) {
                return msg_return('编辑成功！');
            } else {
                return msg_return($roleInfo->getError(), 1);
            }
        } else {
            $id = input('id', 0, 'intval');
            if (!$id) {
                $this->error('参数错误!');
            }
            $info = $RoleDB->getRole(array('id' => $id));
            $this->assign('info', $info);

            return $this->fetch('addrole');
        }
    }

    /**
     *删除角色.
     */
    public function role_del()
    {
        $id = input('param.id', 0, 'intval');
        if (!$id) {
            $this->error('参数错误!');
        }
        $RoleDB = model('Role');
        if ($RoleDB->delRole('id=' . $id)) {
            return json(['msg' => '删除成功！']);
        } else {
            return json(['msg' => '删除失败']);
        }
    }

    /**
     *角色排序.
     */
    public function role_sort()
    {
        $sorts = $_POST['sort'];
        if (!is_array($sorts)) {
            $this->error('参数错误!');
        }
        foreach ($sorts as $id => $sort) {
            model('Role')->upRole(['id' => $id, 'sort' => intval($sort)]);
        }
        $this->success('更新完成！', url('/Admin/User/role'));
    }

    /* ========权限设置部分======== */

    /**
     *权限列表.
     */
    public function access()
    {

        $array = array(
            array('id' => 1, 'pid' => 0, 'name' => '河北省'),
            array('id' => 2, 'pid' => 0, 'name' => '北京市'),
            array('id' => 3, 'pid' => 1, 'name' => '邯郸市'),
            array('id' => 4, 'pid' => 2, 'name' => '朝阳区'),
            array('id' => 5, 'pid' => 2, 'name' => '通州区'),
            array('id' => 6, 'pid' => 4, 'name' => '望京'),
            array('id' => 7, 'pid' => 4, 'name' => '酒仙桥'),
            array('id' => 8, 'pid' => 3, 'name' => '永年区'),
            array('id' => 9, 'pid' => 1, 'name' => '武安市'),
        );

        $roleid = $this->request->param('roleid', 0, 'intval');
        if (!$roleid) {
            $this->error('参数错误!');
        }

        $Tree = new Tree();
        $Tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $Tree->nbsp = '&nbsp;&nbsp;&nbsp;';

        $NodeDB = model('Node');
        $node = obj2arr($NodeDB->getAllNode());

        $AccessDB = model('Access');
        $access = obj2arr($AccessDB->getAllAccess('', 'role_id,node_id,pid,level'));

        //dump($access);exit;

        foreach ($node as $n => $t) {
            $node[$n]['checked'] = ($AccessDB->is_checked($t, $roleid, $access)) ? true : '';
            $node[$n]['depth'] = $AccessDB->get_level($t['id'], $node);
            $node[$n]['pid_node'] = ($t['pid']) ? ' class="tr lt child-of-node-' . $t['pid'] . '"' : '';
        }
        $array = $this->make_tree1($node);

        $this->assign('array', json_encode($array));
        $this->assign('roleid', $roleid);
        return $this->fetch();

    }

    public function make_tree1($list, $pk = 'id', $pid = 'pid', $child = 'list', $root = 0)
    {
        $tree = array();
        foreach ($list as $key => $val) {
            if ($val[$pid] == $root) {
                //获取当前$pid所有子类
                unset($list[$key]);
                if (!empty($list)) {
                    $child = $this->make_tree1($list, $pk, $pid, $child, $val[$pk]); //来来来 找北京的子栏目 递归 空

                    if (!empty($child)) {
                        $val['list'] = $child;
                    }
                }
                $row['name'] = $val['title'];
                $row['checked'] = $val['checked'];
                $row['id'] = $val['id'];
                $row['value'] = $val['id'];
                $row['pid'] = $val['pid'];
                if (isset($val['list'])) {
                    $row['list'] = $val['list'];
                }
                $tree[] = $row;
                //dump($val);
            }
        }
        return $tree;
    }

    /**
     *权限修改.
     */
    public function access_edit()
    {
        $roleid = $this->request->post('roleid', 0, 'intval');
        $nodeid = $_POST['nodeid'];
        if (!$roleid) {
            //$this->error('参数错误!');
            return msg_return('参数错误', 1);
        }
        $AccessDB = model('Access');

        if (is_array($nodeid) && count($nodeid) > 0) { //提交得有数据，则修改原权限配置
            $AccessDB->delAccess(array('role_id' => $roleid)); //先删除原用户组的权限配置

            $NodeDB = model('Node');
            $node = obj2arr($NodeDB->getAllNode());
            foreach ($node as $_v) {
                $node[$_v['id']] = $_v;
            }
            foreach ($nodeid as $k => $node_id) {
                $data[$k] = $AccessDB->get_nodeinfo($node_id, $node);
                $data[$k]['role_id'] = $roleid;
            }
            $AccessDB->saveAll($data); // 重新创建角色的权限配置
        } else { //提交的数据为空，则删除权限配置
            $AccessDB->delAccess(array('role_id' => $roleid));
        }

        return msg_return('设置成功！', 0);
    }
}
