<?php
/*
'''''''''''''''''''''''''''''''''''''''''''''''''''''''''
author:ming    contactQQ:811627583
''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
 */
namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Cache;
use think\Db;

/**
 * 系统配置
 * Class System
 * @package app\admin\controller
 */
class Rank extends AdminBase
{
    public function _initialize()
    {
        parent::_initialize();
    }


      public function index()
      {
          $taskConfig = Db::name('user_rank')->order('id','asc')->select();
          return view()->assign('list',$taskConfig);
      }

    /**
     * 任务修改
     * @return \think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
      public function edit()
      {
          $id = $this->request->param('id');
          $info = Db::name('user_rank')->where('id',$id)->find();
          if ($this->request->isPost()) {
              $data = $this->request->post();
              $data['u_time'] = time();
              $res = Db::name('user_rank')->where('id',$data['id'])->update($data);
              //echo Db::name('task_config')->getLastSql();die;
              $res ? $this->success('修改成功',url('index')) : $this->error('操作失败');
          }
          return view()->assign('info',$info);
      }




}