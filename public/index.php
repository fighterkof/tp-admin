<?php

/**画尚画**/

namespace think;

require __DIR__.'/../thinkphp/base.php';

if (!file_exists('install/lock')) {
    $url = $_SERVER['HTTP_HOST'].trim($_SERVER['SCRIPT_NAME'], 'index.php').'install/index.php';
    header("Location:http://$url");
    die;
}
Container::get('app')->run()->send();
