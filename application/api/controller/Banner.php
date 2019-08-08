<?php

namespace app\api\controller;

class Banner extends Common
{
    /**
     * @description: banner
     *
     * @param {type}
     * @return:
     */
    public function index()
    {
        $map = [];
        $map[] = ['start_time', '<', date('Y-m-d H:i:s')];
        $map[] = ['end_time', '>', date('Y-m-d H:i:s')];
        $map[] = ['status', '=', 1];
        $banner = db('banner')->field(['banner_id', 'ad_name', 'ad_link', 'image', 'mini_app_link'])->where($map)->whereOr(['start_time' => '0000-00-00', 'end_time' => '0000-00-00'])->order('displayorder desc')->limit(5)->select();
        foreach ($banner as $k => $v) {
            if ($v['ad_link']) {
                $banner[$k]['not_empty'] = 1;
            } else {
                $banner[$k]['not_empty'] = 0;
            }
        }
        $this->returnMsg(200, '获取成功', $banner);
    }
}
