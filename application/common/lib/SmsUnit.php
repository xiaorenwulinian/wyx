<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019\6\13 0013
 * Time: 6:25
 */
namespace app\common\lib;

class SmsUnit
{
    const APP_ID = '';
    const APP_SECRET = '';
    protected  static $smsInstance;
    protected function __construct()
    {
    }

    /**
     * 单例模式
     * @return SmsUnit
     */
    public static function getInstance()
    {
        if (is_null(self::$smsInstance)) {
            self::$smsInstance = new self();
        }
        return self::$smsInstance;
    }
    /**
     * 发送短信（单个号码）
     */
    public function sendSmsOne()
    {

    }

    /**
     * 批量发送短信
     */
    public function sendSmsMulti()
    {

    }
}