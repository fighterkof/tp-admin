<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;
use think\Db;

class Index extends Admin
{
    /**
     * 系统首页.
     */
    public function index()
    {
        $Node = obj2arr(model('Node')->getAllNode());
        //print_r($Node);
        $this->assign('Node', $Node);
        $username = session('user_name');
        $roleid = session('roleid');
        if ($username == config('rbac.admin_auth_key')) {
            $sql = 'SELECT `id`,`title` FROM `hsh_node` WHERE ( `status` =1 AND `display`=1 AND `level`<>1 ) ORDER BY sort DESC';
        } else {
            $sql = "SELECT `hsh_node`.`id` as id,`hsh_node`.`title` as title FROM `hsh_node`,`hsh_access` WHERE `hsh_node`.id=`hsh_access`.node_id AND `hsh_access`.role_id=$roleid  AND  `hsh_node`.`status` =1 AND `hsh_node`.`display`=1 AND (`hsh_node`.`level` =0 OR `hsh_node`.`level` =2)  ORDER BY `hsh_node`.sort DESC";
        }
        $main_menu = Db::query($sql);
        foreach ($main_menu as $k => $v) {
            $pid = $v['id'];
            $NodeDB = model('Node');
            $datas = $this->left_child_menu($pid);

            $parent_info = $NodeDB->getNode(array('id' => $pid), 'title');
            $sub_menu_html = '';
            foreach ($datas as $key => $_value) {
                $sub_menu_html .= '<ul class="sub-menu">';
                $sub_array = $this->left_child_menu($_value['id']);
                $sub_menu_html .= '<li><a ><i class="iconfont">&#xe6a7;</i><cite>' . $_value['title'] . '</cite></a>
                <ul class="sub-menu">
                ';
                if (is_array($sub_array)) {
                    foreach ($sub_array as $value) {
                        $href = empty($value['data']) ? 'javascript:void(0)' : url($value['data']);
                        //$sub_menu_html .= "<li><a data-href={$href} data-title={$value['title']} href='javascript:void(0)'>{$value['title']}</a></li>";
                        $sub_menu_html .= '<li> <a onclick="xadmin.add_tab(' . "'{$value['title']}'" . ',' . "'{$href}'" . ')"> <i class="iconfont">&#xe6a7;</i> <cite>' . $value['title'] . '</cite></a></li>';
                    }
                }
                $sub_menu_html .= '</ul></li>';
                $sub_menu_html .= '</ul>';
            }
            $main_menu[$k]['left'] = $sub_menu_html;
        }

        //dump($main_menu);
        //exit;
        //echo '<pre>';
        //print_r($main_menu);
        //exit;
        $this->assign('admin_id', 'admin');

        $this->assign('main_menu', $main_menu);

        return $this->fetch();
    }

    /**
     * 欢迎页面.
     */
    public function welcome()
    {
        return $this->fetch();
    }

    /**
     * 按父ID查找菜单子项.
     *
     * @param int $parentid  父菜单ID
     * @param int $with_self 是否包括他自己
     */
    private function left_child_menu($pid, $with_self = 0)
    {
        $pid = intval($pid);

        $username = session('user_name'); // 用户名
        $roleid = session('roleid'); // 角色ID
        if ($username == config('rbac.admin_auth_key')) { //如果是无视权限限制的用户，则获取所有主菜单
            $sql = "SELECT `id`,`data`,`title` FROM `hsh_node` WHERE ( `status` =1 AND `display`=2 AND `level` <>1 AND `pid`=$pid ) ORDER BY sort DESC";
        } else {
            $sql = "SELECT `hsh_node`.`id` as `id` , `hsh_node`.`data` as `data`, `hsh_node`.`title` as `title` FROM `hsh_node`,`hsh_access` WHERE `hsh_node`.id = `hsh_access`.node_id AND `hsh_access`.role_id = $roleid AND `hsh_node`.`pid` =$pid AND `hsh_node`.`status` =1 AND `hsh_node`.`display` =2 AND `hsh_node`.`level` <>1 ORDER BY `hsh_node`.sort DESC";
        }
        $result = Db::query($sql);

        if ($with_self) {
            $NodeDB = D('Node');
            $result2[] = $NodeDB->getNode(array('id' => $pid), `id`, `data`, `title`);
            $result = array_merge($result2, $result);
        }

        return $result;
    }
}
