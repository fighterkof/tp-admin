<?php

namespace app\hshadmin\event;

use think\Db;

class Base
{
    /**
     * table 表名，不含表前缀  ffff
     * map   查询条件
     * field 筛选字段
     * order_by 字段排序.
     **/
    public function commonlist($table, $map = '', $field = '', $limit = '', $order_by = 'id desc')
    {
        $list = Db::name($table)
                ->field($field)
                ->where($map)
                ->order($order_by)
                ->limit($limit)
                ->paginate(config('ctrl.pagenum'));

        return $list;
    }

    /*
     * table 表名，不含表前缀
     * data 提交的数据
     */
    public function commonadd($table, $data)
    {
        $data['uid'] = session('auth_id');
        $data['add_time'] = time();
        $id = Db::name($table)->insertGetId($data);
        if ($id) {
            return ['code' => 0, 'data' => $id];
        } else {
            return ['code' => 1, 'data' => 'Db0001-写入数据库出错！'];
        }
    }

    /*
     * table 表名，不含表前缀
     * data 提交的数据
     */
    public function commonedit($table, $data)
    {
        $ret = Db::name($table)->where('id', $data['id'])->update($data);
        if ($ret) {
            return ['code' => 0, 'data' => $ret];
        } else {
            return ['code' => 1, 'data' => 'Db0002-更新数据库出错！'];
        }
    }

    /*
     * table 表名，不含表前缀
     * data 提交的数据
     */
    public function commondel($table, $id)
    {
        $ret = Db::name($table)->delete($id);
        if ($ret) {
            return ['code' => 0, 'data' => $ret];
        } else {
            return ['code' => 1, 'data' => 'Db0003-数据库删除数据出错！'];
        }
    }
}
