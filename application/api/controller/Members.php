<?php

namespace app\api\controller;

use think\facade\Request;
use think\Validate;

class Members extends Common
{
    /**
     * @description: 获取微信用户基础信息
     *
     * @param {type}
     * @return:
     */
    public function information()
    {
        $mem = model('Members');
        $this->datas = Request::param();
        $openid = $this->datas['openid'];
        $data = array(
            'openid' => $openid,
            'nickname' => $this->datas['nickname'],
            'avatar' => $this->datas['avatar'],
            'address' => $this->datas['address'],
            'createtime' => time(),
        );
        $this->checkopenid();
        $members_info = $mem->getMembersInfo($openid);
        if (!$members_info) {
            $members = db('members')->insert($data);
            if ($members) {
                $this->returnMsg(200, '获取成功');
            } else {
                $this->returnMsg(400, '获取失败');
            }
        }
        $this->returnMsg(200, '获取成功');
    }

    /**
     * @description: 检查openid是否为空
     *
     * @param {type}
     * @return:
     */
    protected function checkopenid()
    {
        $rule = [
            'openid' => 'require',
            'nickname' => 'require',
            'avatar' => 'require',
        ];

        $msg = [
            'openid.require' => 'openid不能为空!',
            'nickname.require' => 'nickname不能为空!',
            'avatar.require' => 'avatar不能为空!',
        ];

        $data = [
            'openid' => $this->datas['openid'],
            'nickname' => $this->datas['nickname'],
            'avatar' => $this->datas['avatar'],
        ];
        $validate = Validate::make($rule)->message($msg);
        $result = $validate->check($data);

        if (!$result) {
            $this->returnMsg(400, $validate->getError());
        }
    }
}
