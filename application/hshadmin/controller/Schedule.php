<?php

namespace app\hshadmin\controller;

use app\common\controller\admin;
use think\facade\App;

class Schedule extends Admin
{
    //课程列表
    public function index()
    {

        return $this->fetch();
    }

}
