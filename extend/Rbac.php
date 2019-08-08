<?php

/**画尚画**/

use think\Db;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;

class Rbac
{
    public static function authenticate($map, $model = '')
    {
        if (empty($model)) {
            $model = config('rbac.user_auth_model');
        }

        return db($model)->where($map)->find();
    }

    public static function saveAccessList($authId = null)
    {
        if (null === $authId) {
            $authId = session(config('rbac.user_auth_key'));
        }
        // 如果使用普通权限模式，保存当前用户的访问权限列表
        if (config('rbac.user_auth_type') != 2 && !session(config('rbac.admin_auth_key'))) {
            session('_ACCESS_LIST', RBAC::getAccessList($authId));
        }

        return;
    }

    public static function getRecordAccessList($authId = null, $module = '')
    {
        if (null === $authId) {
            $authId = session(config('rbac.user_auth_key'));
        }
        if (empty($module)) {
            $module = Request::instance()->controller();
        }
        $accessList = RBAC::getModuleAccessList($authId, $module);

        return $accessList;
    }

    //检查当前操作是否需要认证
    public static function checkAccess()
    {
        //如果项目要求认证，并且当前模块需要认证，则进行权限认证
        if (config('rbac.user_auth_on')) {
            $_module = array();
            $_action = array();
            if ('' != config('rbac.require_auth_controller')) {
                //需要认证的模块
                $_module['yes'] = explode(',', strtoupper(config('rbac.require_auth_controller')));
            } else {
                //无需认证的模块
                $_module['no'] = explode(',', strtoupper(config('rbac.not_auth_controller')));
            }

            //检查当前模块是否需要认证
            if ((!empty($_module['no']) && !in_array(strtoupper(Request::instance()->controller()), $_module['no'])) || (!empty($_module['yes']) && in_array(strtoupper(Request::instance()->controller()), $_module['yes']))) {
                if ('' != config('rbac.require_auth_action')) {
                    //需要认证的操作
                    $_action['yes'] = explode(',', strtoupper(config('rbac.require_auth_action')));
                } else {
                    //无需认证的操作
                    $_action['no'] = explode(',', strtoupper(config('rbac.not_auth_action')));
                }
                //检查当前操作是否需要认证
                if ((!empty($_action['no']) && !in_array(strtoupper(Request::instance()->action()), $_action['no'])) || (!empty($_action['yes']) && in_array(strtoupper(Request::instance()->action()), $_action['yes']))) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        return false;
    }

    // 登录检查
    public static function checkLogin()
    {
        //检查当前操作是否需要认证
        if (RBAC::checkAccess()) {
            //检查认证识别号
            if (!session(config('rbac.user_auth_key'))) {
                if (config('guest_auth_on')) {
                    // 开启游客授权访问
                    if (!session('?_ACCESS_LIST')) {
                        // 保存游客权限
                        RBAC::saveAccessList(config('rbac.guest_auth_id'));
                    }
                } else {
                    // 禁止游客访问跳转到认证网关
                    redirect(PHP_FILE.config('rbac.user_auth_gateway'));
                }
            }
        }

        return true;
    }

    //权限认证的过滤器方法
    public static function AccessDecision()
    {
        //检查是否需要认证
        $appName = Request::instance()->module();
        if (Rbac::checkAccess()) {
            //存在认证识别号，则进行进一步的访问决策
            $accessGuid = md5(Request::instance()->controller().Request::instance()->action());

            if (!Session::has(Config::get('rbac.admin_auth_key'))) {
                if (config('rbac.user_auth_type') == 2) {
                    //加强验证和即时验证模式 更加安全 后台权限修改可以即时生效
                    //通过数据库进行访问检查
                    $accessList = RBAC::getAccessList(session(config('rbac.user_auth_key')));
                } else {
                    // 如果是管理员或者当前操作已经认证过，无需再次认证
                    if (session($accessGuid)) {
                        return true;
                    }
                    //登录验证模式，比较登录后保存的权限访问列表
                    $accessList = session('_ACCESS_LIST');
                }
                //判断是否为组件化模式，如果是，验证其全模块名
                $module = defined('P_MODULE_NAME') ? P_MODULE_NAME : Request::instance()->controller();
                $action = strtoupper(Request::instance()->action());
                //dump($accessList);
                //echo $appName.'---'.$module.'----'.$action;
                //dump($accessList[strtoupper($appName)]);
                //exit;
                if (!isset($accessList[strtoupper($appName)][strtoupper($module)][$action])) {
                    session($accessGuid, false);

                    return false;
                } else {
                    session($accessGuid, true);
                }
            } else {
                //管理员无需认证
                return true;
            }
        }

        return true;
    }

    /**
     * 判断多维数组是否存在$key中的key.
     *
     * @param $multi
     * @param $key
     *
     * @return bool
     */
    private static function keyExist($multi, $key)
    {
        $tmp = $multi;
        while ($k = array_shift($key)) {
            if (isset($tmp[$k])) {
                echo $k;
                $tmp = $tmp[$k];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * 取得当前认证号的所有权限列表.
     *
     * @param $authId
     *
     * @return array
     */
    public static function getAccessList($authId)
    {
        // 表前缀
        $table_prefix = Config::get('database.prefix');
        $table = [
            'role' => $table_prefix.Config::get('rbac.role_table'),
            'user' => $table_prefix.Config::get('rbac.user_table'),
            'access' => $table_prefix.Config::get('rbac.access_table'),
            'node' => $table_prefix.Config::get('rbac.node_table'),
        ];
        $sql = 'select node.id,node.name from '.
            $table['role'].' as role,'.
            $table['user'].' as user,'.
            $table['access'].' as access ,'.
            $table['node'].' as node '.
            "where user.user_id='{$authId}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1 and access.node_id=node.id and node.level=1 and node.status=1";
        $apps = Db::query($sql);
        $access = array();
        foreach ($apps as $key => $app) {
            $appId = $app['id'];
            $appName = $app['name'];
            // 读取项目的模块权限
            $access[strtoupper($appName)] = array();
            $sql = 'select node.id,node.name from '.
            $table['role'].' as role,'.
            $table['user'].' as user,'.
            $table['access'].' as access ,'.
            $table['node'].' as node '.
            // 注释SQL是原版的语句，但由于我在系统中加入了非节点类型菜单(level=0)，
            // 所以 and node.pid={$appId} 这句会导致上层有非节点类型菜单的模块无法查询到
            //"where user.user_id='{$authId}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1 and access.node_id=node.id and node.level=2 and node.pid={$appId} and node.status=1";
            "where user.user_id='{$authId}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1 and access.node_id=node.id and node.level=2 and node.status=1";
            $modules = Db::query($sql);

            // 判断是否存在公共模块的权限
            $publicAction = array();
            foreach ($modules as $key => $module) {
                $moduleId = $module['id'];
                $moduleName = $module['name'];
                if ('PUBLIC' == strtoupper($moduleName)) {
                    $sql = 'select node.id,node.name from '.
                        $table['role'].' as role,'.
                        $table['user'].' as user,'.
                        $table['access'].' as access ,'.
                        $table['node'].' as node '.
                        "where user.user_id='{$authId}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1 and access.node_id=node.id and node.level=3 and node.pid={$moduleId} and node.status=1";
                    $rs = Db::query($sql);
                    foreach ($rs as $a) {
                        $publicAction[$a['name']] = $a['id'];
                    }
                    unset($modules[$key]);
                    break;
                }
            }

            // 依次读取模块的操作权限
            foreach ($modules as $key => $module) {
                $moduleId = $module['id'];
                $moduleName = $module['name'];
                $sql = 'select node.id,node.name from '.
                    $table['role'].' as role,'.
                    $table['user'].' as user,'.
                    $table['access'].' as access ,'.
                    $table['node'].' as node '.
                    "where user.user_id='{$authId}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1 and access.node_id=node.id and node.level=3 and node.pid={$moduleId} and node.status=1";
                $rs = Db::query($sql);

                $action = array();
                foreach ($rs as $a) {
                    $action[$a['name']] = $a['id'];
                }
                // 和公共模块的操作权限合并
                $action += $publicAction;
                $access[strtoupper($appName)][strtoupper($moduleName)] = array_change_key_case($action, CASE_UPPER);
            }
        }
        //转化为树
        //$tree = list_to_tree($apps);
        //递归生成权限树
        // $ret = self::treeToMultiArray($tree, "name", "id", "_child");
        //dump($access);
        return $access;
    }

    /**
     * 将树递归成多维数组.
     *
     * @param array        $tree        树
     * @param string       $key         放入多维数组里的键名
     * @param string|array $key_default 默认值，如果是数组[VALUE]，则为当前数组子项的键名，如果是其他就是传入的值
     * @param string       $key_child   子节点键名
     *
     * @return array
     */
    private static function treeToMultiArray($tree, $key = 'name', $key_default = 'id', $key_child = '_child')
    {
        $return = [];
        if (is_array($tree)) {
            foreach ($tree as $v) {
                // 默认值
                if (isset($v[$key_child])) {
                    $default = self::treeToMultiArray($v[$key_child], $key, $key_default, $key_child);
                } else {
                    $default = $v[$key_default];
                }

                // 存在高阶节点，转为多维数组 (one.two/index 或 one/index 类型高阶节点)
                $nodes = explode('/', strtoupper(str_replace('.', '/', $v[$key])));
                $return = array_merge_multi($return, self::arrayOneToMulti($nodes, count($nodes), $default));
            }
        }

        return $return;
    }

    /**
     * 一维数组转多维数组.
     *
     * @param array        $source  原一维数组
     * @param int          $length  原一维数组长度
     * @param string|array $default 多维数组默认值
     * @param int          $i       开始索引
     * @param array        $target  转化为的目标数组
     *
     * @return array
     */
    private static function arrayOneToMulti($source, $length, $default = '', $i = 0, $target = [])
    {
        if ($i == $length - 1) {
            $target[$source[$i]] = $default;
        } else {
            $target[$source[$i]] = self::arrayOneToMulti($source, $length, $default, $i + 1, $target);
        }

        return $target;
    }
}
