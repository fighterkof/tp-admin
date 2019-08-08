<?php

namespace app\hshadmin\model;

use think\Model;

class Consumer extends Model
{
    /**
     * @description: 根据consumer_id来获取会员信息
     * @param {type}
     * @return:
     */
    public function getConsumerByCid($consumer_id)
    {

        $info = db('consumer')->where(['consumer_id' => $consumer_id])->find();

        return $info;

    }
}
