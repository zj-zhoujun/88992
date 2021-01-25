<?php
/*
'''''''''''''''''''''''''''''''''''''''''''''''''''''''''
author:ming    contactQQ:811627583
''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
 */
namespace app\common\model;

use think\Model;
use think\Db;
class Wealth extends Model
{
    protected $table = 'wym_money_log';
    public function getCurrencyAttr($value)
    {
        $currency = [
            //'money'=>'现金币',
            'pay_points' => '微分',
            'share_integral' => '收益转存',
            'team_integral' => '团队收益',

        ];
        return $currency[$value];
    }


}