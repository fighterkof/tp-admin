<?php
/**
 * Rbac.
 */
return [
    // RBAC 权限验证user_auth_key
    'user_auth_on' => true, // 是否开启认证
    'user_auth_type' => 2, // 默认认证类型 1 - 登录认证 | 2 - 实时认证
    'user_auth_key' => 'auth_id',    // 用户认证 SESSION 标记
    'admin_auth_key' => 'admin',
    'user_auth_model' => 'User',    // 默认验证数据表模型
    'user_auth_gateway' => 'hshadmin/login/login',    // 默认认证网关
    'not_auth_controller' => 'Index,Info,common,Upload,WF,Fd,Ueditor',        // 默认无需认证控制器，多级控制器使用点语法，严格大小写，多个逗号隔开
    'require_auth_controller' => '',        // 默认需要认证控制器，多级控制器使用点语法，严格大小写，多个逗号隔开
    'not_auth_action' => '',        // 默认无需认证方法，全部小写，多个逗号隔开
    'require_auth_action' => '',        // 默认需要认证方法，全部小写，多个逗号隔开
    'common_auth_name' => 'common,pub',   // 公共授权控制器名称和方法名称
    'guest_auth_on' => false,    // 是否开启游客授权访问
    'guest_auth_id' => 0,     // 游客的用户ID

    'role_table' => 'role', // 角色表名称，不包含表前缀
    'user_table' => 'role_user', // 用户角色关系表名称，不包含表前缀
    'access_table' => 'access', // 权限表名称，不包含表前缀
    'node_table' => 'node', // 节点表名称，不包含表前缀
];
