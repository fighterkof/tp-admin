<?php

namespace app\hshadmin\model;

use think\Model;

class Art extends Model
{

    /**
     * @description: 等级id来获取详细信息
     * @param {type}
     * @return:
     */
    public function getArtLevelByLid($level_id)
    {

        $result = db('art_level')->where(['id' => $level_id])->find();
        return $result;
    }

    /**
     * @description: 根据consumer_id来获取画家信息
     * @param {type}
     * @return:
     */
    public function getArtByCid($consumer_id)
    {

        $info = db('art')->where(['consumer_id' => $consumer_id])->find();

        return $info;

    }
    /**
     * @description: 获取画家等级列表
     * @param {type}
     * @return:
     */
    public function getArtLevel($keyword = "")
    {

        $map = array();
        if ($keyword) {
            $map[] = ['name', 'like', "%{$keyword}%"];
        }
        $list = db('art_level')->where($map)->order(['displayorder' => 'desc', 'id' => 'desc'])->select();
        return $list;

    }

    /**
     * @description: 获取画家创作媒介
     * @param {type}
     * @return:
     */
    public function getArtCat($keyword = "")
    {

        $map[] = ['parent_id', '=', 0];
        $map[] = ['status', '=', 1];
        if ($keyword) {
            $map[] = ['cat_name', 'like', "%{$keyword}%"];
        }
        $list = db('art_cat')->where($map)->order(['displayorder' => 'desc', 'cat_id' => 'desc'])->select();

        foreach ($list as $k => $v) {
            $list[$k]['par'] = db('art_cat')->where(['parent_id' => $v['cat_id'], 'status' => 1])->select();
        }

        return $list;
    }

    public function getArtCatByPid()
    {

    }

    public function getArtCatByid()
    {

    }
}
