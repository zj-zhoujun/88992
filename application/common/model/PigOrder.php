<?php
namespace app\common\model;

use think\Db;
use think\Model;

class PigOrder extends Model
{

//    public function pigLevel($money)
//    {
//        $pigLevel = $this->where('max_price','egt',$money)->order('max_price','asc')->find();
//        return $pigLevel;
//    }
    public function cancel($id)
    {
        $orderInfo = $this->find($id);
        $re = $this->where('id',$id)
            ->setField([
                'status'=>0,
                'uid' => $orderInfo['sell_id'],
                'sell_id' => 0
            ]);
        return $re ? true : false;
    }
    public function confirm($id)
    {
        $re =$this
            ->where('id',$id)
            ->setField(['status'=>3,'update_time'=>time()]);
        if($re){

            $orderInfo = $this->find($id);
            //把猪添加到买方
            $userPig = [];
            //$order
            $pigInfo = Db::name('task_config')->where('id',$orderInfo['pig_id'])->find();
            $baseConfig = unserialize(Db::name('system')->where('name','base_config')->value('value'));

            $sell_end_time = $baseConfig['sell_end_time'];
            if(!isset($sell_end_time) || $sell_end_time<0){
                $sell_end_time = 0;
            }

            $userPig['uid'] = $orderInfo['uid'];
            //$userPig['order_id'] = $id;
            //$userPig['pig_id'] = $orderInfo['pig_id'];
            //$userPig['pig_name'] = $orderInfo['pig_name'];
            //$userPig['pig_no'] = create_trade_no();
            //$userPig['cycle'] = $pigInfo['cycle'];
            //$userPig['contract_revenue'] = $pigInfo['contract_revenue'];
            //$userPig['doge'] = $pigInfo['doge'];
            $userPig['status'] = 0;
            $userPig['from_id'] = $orderInfo['sell_id'];
            $userPig['price'] = $orderInfo['price'];
            $userPig['create_time'] = time();
            $userPig['end_time'] = time()+$pigInfo['cycle']*60-$sell_end_time;
            Db::name('user_pigs')->where('order_id',$id)->update($userPig);
            //增加推荐人推荐的购买次数,满足条件升级绿色通道
            Db::name('user')->where('id',$orderInfo['uid'])->setInc('self_buy',1);
            $self_buy = Db::name('user')->where('id',$orderInfo['uid'])->value('self_buy');
            if($self_buy==1){
                $intro_ids = Db::name('user_relation')->where('uid',$orderInfo['uid'])->value('rel');
                $intro_ids = explode(',',$intro_ids);
                Db::name('user')->where(['id'=>['in',$intro_ids]])->setInc('tj_buy',1);
                foreach($intro_ids as $intro_id){
                    if(!$intro_id){
                        continue;
                    }
                    $tj_buy = Db::name('user')->where('id',$intro_id)->value('tj_buy');
                    $user_rank_set = Db::name('user_rank')->where(['level'=>['in',[30,40]]])->order('level desc')->column('team','level');
                    foreach($user_rank_set as $level=>$team){
                        if($tj_buy>=$team){
                            model('User')->uplv($intro_id,$level);
                            break;
                        }
                    }
                }
            }


            //销毁原来的猪
//            $map = [];
//            $map['order_id'] = $id;
//            $map['uid'] = $orderInfo['sell_id'];
//            Db::name('user_pigs')->where($map)->setField('status',2);
            //奖励PIG
            moneyLog($orderInfo['uid'],0,'pig',$pigInfo['pig'],9,'买入奖励wia');

            //奖金记录
            addReward($orderInfo['uid'],0,'pig',$pigInfo['pig'],5,'交易奖励wia');
            return true;
        } else{
            //$this->error('操作失败');
            return false;
        }
    }
}