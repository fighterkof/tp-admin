<?php

/**画尚画**/

return [
    'id' => '',
    // SESSION_ID的提交变量,解决flash上传跨域
    'var_session_id' => '',
    // SESSION 前缀
    'prefix' => 'hshthink',
    // 驱动方式 支持redis memcache memcached
    'type' => '',
    // 是否自动开启 SESSION
    'auto_start' => true,
    'expire' => 14400,
];
