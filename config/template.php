<?php

/**画尚画**/

return [
    // 模板引擎类型 支持 php think 支持扩展
    'type' => 'Think',
    // 模板路径
    'view_path' => '',
    // 模板后缀
    'view_suffix' => 'html',
    // 模板文件名分隔符
    'view_depr' => DIRECTORY_SEPARATOR,
    // 预先加载的标签库
    'taglib_pre_load' => '',
    // 模板引擎普通标签开始标记
    'tpl_begin' => '{',
    // 模板引擎普通标签结束标记
    'tpl_end' => '}',
    // 标签库标签开始标记
    'taglib_begin' => '{',
    // 标签库标签结束标记
    'taglib_end' => '}',
     // 视图输出字符串内容替换
    'tpl_replace_string' => [
        '__HUI__' => '/static/h-ui',
        '__HUIADMIN__' => '/static/h-ui.admin',
        '__LIB__' => '/static/lib',
        '__Flow__' => '/static/work',
        '__FD__' => '/static/fd',
        '__STATIC__' => '/static',
        '__UPLOADS__' => '/uploads',
    ],
];
