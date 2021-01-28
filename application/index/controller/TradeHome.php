<?php
/*
'''''''''''''''''''''''''''''''''''''''''''''''''''''''''
author:ming    contactQQ:811627583
''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
 */
namespace app\index\controller;

use app\common\controller\IndexBase;
use think\Cache;
use think\Controller;
use think\Db;
use My\DataReturn;

class TradeHome extends IndexBase
{
    //首页
    public function index()
    {
        $id = input('id');
        $user = $this->user;
        $rank_info = Db::name('user_rank')->where('level',$user['user_rank'])->find();

        $prep = 0;//提前入场时间
        if($user['user_rank']>0 ){
            $prep = $rank_info['prep'];
        }
        //判断会员级别是否到期
        if($rank_info['days']>0){
            $end_time = $user['rank_time']+86400*$rank_info['days'];
            if($end_time<=time()){
                Db::name('user')->where('id',$this->user_id)->update(['user_rank'=>0,'rank_time'=>0]);
                $prep = 0;
            }
        }
        $pigInfo =  Db::name('task_config')->where('id',$id)->find();
        $start_time = $pigInfo['start_time'];//开抢时间

        $enter_time = date('H:i',strtotime($start_time)-$prep*60);

        if ($enter_time>date('H:i') || date('H:i')>$pigInfo['end_time'])
        {
            $this->error('不是开抢时间,不可进场');
        }

        $pigMap = [];
        $pigMap['p.pig_id'] = $id;
        $pigMap['p.status'] = 0;
        $pigMap['uid'] = ['neq',$this->user_id];
        $piglist = Db::name('pig_order')
            ->alias('p')
            ->join('user u','u.id=p.uid')
            ->where($pigMap)
            ->field('p.id,p.price,p.order_no,u.mobile,u.head_img')
            ->select();
        $this->assign('list',$piglist);
        return $this->fetch();
    }





}
