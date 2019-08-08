<?php

namespace app\api\controller;

use think\facade\Request;
use think\Validate;

class My extends Common
{
    /**
     * @description: 家长完善信息
     *
     * @param {type}
     * @return:
     */
    public function perfect()
    {
        $mem = model('Members');
        $this->datas = Request::param();
        $openid = $this->datas['openid'];
        if (!$openid) {
            $this->returnMsg(400, '参数错误');
        }
        $data = array(
            'parent_name' => $this->datas['parent_name'],
            'phone' => $this->datas['phone'],
        );
        $this->checkPhone();
        $members = $mem->getMembersInfo($openid);
        if ($members['type'] == 2 && $members['parent_name'] && $members['phone']) {
            $this->returnMsg(400, '你已完善信息，请勿重复提交');
        }
        $update_members = db('members')->where(['openid' => $openid])->update($data);
        if ($update_members) {
            $this->returnMsg(200, '提交成功');
        } else {
            $this->returnMsg(400, '提交失败');
        }
    }

    /**
     * @description: 检查手机号码是否正确
     *
     * @param {type}
     * @return:
     */
    protected function checkPhone()
    {
        $rule = [
            'parent_name' => 'require',
            'phone' => 'require|mobile',
        ];

        $msg = [
            'parent_name.require' => '家长姓名不能为空!',
            'phone.require' => '手机号不能为空!',
            'phone.mobile' => '手机号码不正确!',
        ];

        $data = [
            'parent_name' => $this->datas['parent_name'],
            'phone' => $this->datas['phone'],
        ];
        $validate = Validate::make($rule)->message($msg);
        $result = $validate->check($data);

        if (!$result) {
            $this->returnMsg(400, $validate->getError());
        }
    }
}
