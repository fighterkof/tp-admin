<?php

namespace app\api\model;

use think\Model;

class Members extends Model
{
    /**
     * @description: 根据opneid查询用户信息
     *
     * @param {type}
     * @return:
     */
    public function getMembersInfo($openid)
    {
        $members = db('members')->where(['openid' => $openid])->find();
        return $members;
    }
}
