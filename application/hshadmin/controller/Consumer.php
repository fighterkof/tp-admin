<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;
use think\facade\App;

class Consumer extends Admin
{
    public static $treeList = array();
    /**
     * @description: 会员列表
     *
     * @param {type}
     * @return:
     */
    public function index()
    {

        $Art = model('Art');
        $pagenumber = config('ctrl.pagenum'); //获取没页的页数量
        $keyword = input('keyword');
        $map = array();
        if ($keyword) {
            $map[] = ['phone', 'like', "%{$keyword}%"];
        }

        $list = db('consumer')->where($map)->order(['consumer_id' => 'desc'])->paginate($pagenumber, false, ['query' => request()->param()])->each(function ($item, $key) use ($Art) {
            $art_info = $Art->getArtByCid($item['consumer_id']);

            return $item;
        });

        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }
    /**
     * @description: 查看会员
     *
     * @param {type}
     * @return:
     */
    public function seesumer()
    {
        $consumer_id = input('consumer_id', 0, 'intval');
        if (!$consumer_id) {
            return msg_return('参数错误！', 1);
        }
        $info = db('consumer')->where('consumer_id', $consumer_id)->find();
        $this->assign('info', $info);
        return $this->fetch();
    }
    /**
     * @description: 修改会员
     *
     * @param {type}
     * @return:
     */
    public function editsumer()
    {
        $consumer_id = input('consumer_id', 0, 'intval');
        if (!$consumer_id) {
            return msg_return('参数错误！', 1);
        }
        $info = db('consumer')->where('consumer_id', $consumer_id)->find();
        if ($this->request->isPost()) {
            $data = input('post.');
            if (!$data['password']) {
                $data['password'] = $info['password'];
            } else {
                $data['password'] = sha1(md5($data['password']));
            }
            if (!$data['pay_password']) {
                $data['pay_password'] = $info['pay_password'];
            } else {
                $data['pay_password'] = sha1(md5($data['pay_password']));
            }
            db('consumer')->where(['consumer_id' => $consumer_id])->update($data);

            return msg_return('修改成功！');
        }
        $this->assign('info', $info);
        return $this->fetch();
    }
    /**
     * @description: 删除会员
     *
     * @param {type}
     * @return:
     */
    public function delsumer()
    {
        $consumer_id = input("get.consumer_id");
        if (!$consumer_id) {
            return msg_return('参数错误！', 1);
        }

        db('consumer')->where('consumer_id', $consumer_id)->delete();

        return msg_return('删除成功！');
    }

    /**
     * @description: 设置为画家
     * @param {type}
     * @return:
     */
    public function setart()
    {

        $consumer_id = input('consumer_id', 0, 'intval');
        if (!$consumer_id) {

            $this->error('参数错误！', url('consumer/index'));
        }

        $info = db('consumer')->where('consumer_id', $consumer_id)->find();
        if ($info['status'] != '1') {

            $this->error('用户被禁止登录，请先解除!', url('consumer/index'));
        }
        $Art = model('Art');
        $art_info = $Art->getArtByCid($consumer_id);
        $this->assign('art_info', $art_info);
        $titles = [];
        $awards = [];
        if ($art_info) {
            $atc = db('art_to_cat')->field('cat_id')->where(['art_id' => $art_info['art_id']])->cursor();
            $atc_ids = [];
            foreach ($atc as $v) {
                $atc_ids[] = $v['cat_id'];
            }
            $this->assign('atc_ids', $atc_ids); //创作媒介
            $art_tutor_ids = unserialize($art_info['art_tutor_id']); //辅导范围
            $this->assign('art_tutor_ids', $art_tutor_ids);

            $titles = unserialize($art_info['title']); //头衔
            $this->assign('titles', $titles);

            $awards = unserialize($art_info['award']); //辅导范围
            $this->assign('awards', $awards);

        }

        if ($this->request->isPost()) {

            $art_cat_ids = input('post.art_cat_id');
            if (empty($art_cat_ids)) {
                return msg_return('最少要选一个创作媒介！');
            }

            $art_id = input('post.art_id');
            $level_id = input('post.level_id', 0, 'intval');
            $data = array(
                'true_name' => input('post.true_name'),
                'level_id' => $level_id,
                'consumer_id' => $consumer_id,
                'person_content' => input('post.person_content'),
                'short_conent' => input('post.short_conent'),
                'art_tutor_id' => serialize(input('post.art_tutor_id')),
                'appraisal_price' => input('post.appraisal_price'),
                'address' => input('post.address'),
                'country' => input('post.country'),
                'national' => input('post.national'),
                'birth' => input('post.birth'),
                'birth_place_prov' => input('post.birth_place_prov'),
                'birth_place_city' => input('post.birth_place_city'),
                'school' => input('post.school'),
                'title' => serialize(array_filter(input('post.title'))),
                'award' => serialize(array_filter(input('post.award'))),
                'other' => input('post.other'),
                'status' => input('post.status', 0, 'intval'),
            );

            if ($art_id) {
                db('art')->where(['art_id' => $art_id, 'consumer_id' => $consumer_id])->update($data);
            } else {
                $art_id = db('art')->insertGetId($data);
            }
            $adata = [];
            foreach ($art_cat_ids as $id) {
                $adata[] = array(
                    'cat_id' => $id,
                    'art_id' => $art_id,
                );
            }
            db('art_to_cat')->where(['art_id' => $art_id])->delete();
            db('art_to_cat')->insertAll($adata);

            return msg_return('设置成功！');
        }

        $level_list = $Art->getArtlevel();
        $this->assign('level_list', $level_list);

        $this->assign('info', $info);

        $cat_list = $Art->getArtCat();
        $this->assign('cat_list', $cat_list);

        return $this->fetch();

    }

}
