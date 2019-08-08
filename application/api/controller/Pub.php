<?php

namespace app\api\controller;

use function GuzzleHttp\json_encode;
use think\Controller;

class Pub extends Controller
{
    public function df()
    {

        $arr = config('local.');
        dump(json_decode($arr['log'], true));
    }
    /**
     * 不符合规则的到这来来.
     *
     * @return \think\Response
     */
    public function miss()
    {
        $return_data['code'] = 400;
        $return_data['msg'] = '你居然找到这来的？';
        $return_data['data'] = [];

        echo json_encode($return_data);
        die;
    }

    /**
     * @description: 上线后必须要毁灭
     *
     * @param {type}
     * @return:
     */
    public function echoToken()
    {
        $appkey = '!h@s#h$.%c^o&m*';
        $time = time();
        // $time = '1555056208';
        $random = '123456';
        $session_token = $time . $random;
        $session_token = md5($appkey . $session_token . $appkey);

        echo $time . '<br />';
        echo '123456' . '<br />';
        echo $session_token . '<br />';
        echo md5(1);
        echo '==';
        echo md5('1');
    }
}
