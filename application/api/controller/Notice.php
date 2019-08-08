<?php

namespace app\api\controller;

class Notice extends Common
{
    /**
     * @description: 公告
     *
     * @param {type}
     * @return:
     */
    public function index()
    {
        $map = [];
        $map[] = ['status', '=', 1];
        $notice = db('notice')->field(['notice_id', 'type', 'title'])->where($map)->order('displayorder desc')->select();
        foreach ($notice as $k => $v) {
            if ($v['type'] == 1) {
                $notice[$k]['type'] = '活动';
            } else {
                $notice[$k]['type'] = '通知';
            }
        }
        $this->returnMsg(200, '获取成功', $notice);
    }

    /**
     * @description: 公告详情
     *
     * @param {type}
     * @return:
     */
    public function details()
    {
        $notice_id = input('notice_id', 0, 'intval');
        if (!$notice_id) {
            $this->returnMsg(400, '参数错误');
        }
        $notice_details = db('notice')->field(['notice_id', 'title', 'show_time', 'content'])->where(['notice_id' => $notice_id, 'status' => 1])->find();
        $notice_details['show_time'] = date('Y-m-d', $notice_details['show_time']);
        $notice_details['content'] = html_entity_decode($notice_details['content']);
        $this->returnMsg(200, '获取成功', $notice_details);
    }
}
