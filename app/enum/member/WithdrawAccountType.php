<?php
declare(strict_types=1);

namespace app\enum\member;



use core\interface\IEnum;

/**
 * 提现账号类型枚举
 */
enum WithdrawAccountType: int implements IEnum
{

    /**
     * 银行卡
     */
    case BANK = 1;

    /**
     * 支付宝
     */
    case ALIPAY = 2;

    /**
     * 微信
     */
    case WECHAT = 3;

    /**
     * 获取枚举文本
     */
    public function label(): string
    {
        return match ($this) {
            self::BANK => '银行卡',
            self::ALIPAY => '支付宝',
            self::WECHAT => '微信',
        };
    }
}