<?php

namespace app\api\controller;

use think\Controller;
use think\facade\Request;

class Common extends Controller
{
    protected $setting; //APP配置
    protected $req; //用来处理客户端传递过来的参数
    protected $appkey = '!h@s#h$.%c^o&m*';

    public function initialize()
    {
        parent::initialize();
        $this->req = Request::param();
        //获取app配置
        $this->setting = config('api.');
        //1. 检车请求时间是否超时
        $this->checkTime(Request::only('time'));
        $this->checkToken(Request::only(['time', 'token', 'random']));
    }

    //检测请求的时间是否超时
    public function checkTime($arr)
    {
        $setting = $this->setting;
        //$this->returnMsg(400, '请求超时!');
        if (!isset($arr['time']) || intval($arr['time']) <= 1) {
            $this->returnMsg('400', '时间戳不存在');
        }
        if (time() - intval($arr['time']) > $setting['general']['token_time']) {
            $this->returnMsg('400', '请求超时！');
        }
    }

    //返回信息
    protected function returnMsg($code, $msg = '', $data = [], $page = '')
    {
        returnMsg($code, $msg, $data, $page);
    }

    //验证token方法 (防止篡改数据)
    /*
    $arr: 全部请求参数
    return : json
     */
    protected function checkToken($arr)
    {
        //检测客户端是否传递过来token数据
        if (!isset($arr['token']) || empty($arr['token'])) {
            $this->returnMsg(400, 'token不能为空');
        }

        //这是客户端api传递过来的token
        $app_token = $arr['token'];

        //如果已经传递token数据，就删除token数据，生成服务端token与客户端的token做对比
        unset($arr['token']);
        ///appkey+时间+随机+appkey==token
        $session_token = implode($arr);

        $session_token = md5($this->appkey . $session_token . $this->appkey);

        //echo $session_token;die; //调试输出

        //如果传递过来的token不相等
        if ($app_token !== $session_token) {
            $this->returnMsg(400, 'token值不正确');
        }
    }
}
