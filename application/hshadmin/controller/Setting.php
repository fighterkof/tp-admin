<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;
use think\facade\App;
use think\facade\Config;

class Setting extends Admin
{
    /**
     * @description: app配置
     *
     * @param {type}
     * @return:
     */
    public function index()
    {
        $this->assign('info', config('api.'));
        if ($this->request->isPost()) {
            $url = App::getRootPath();
            $config = input('post.');
            arr2file($url . '/config/api.php', $config);
            //清缓存
            $this->get('http://47.106.72.178/bak/ocp.php?RESET=1');
            return msg_return('更新成功！');
        }

        return $this->fetch();
    }

    private function get($url)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }
}
