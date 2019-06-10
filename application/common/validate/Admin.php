<?php
namespace app\common\validate;
use think\Validate;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\12\22 0022
 * Time: 16:29
 */
class Admin extends Validate {
    //验证规则
    protected $rule = [
        'username'  =>  'require|max:60',
        'password'  =>  'require|max:60',
        'password_confirm'=>'require|confirm:password',
        'id' =>  'require|number',
    ];

    protected $message = [
        'username.require'  =>  '标题必须填写',
        'password.require'  =>  '密码必须填写',
        'password_confirm.confirm'  =>  '密码和确认密码不一致',
        'id.number' =>  'id为数字',
    ];

    //使用场景
    protected $scene = [
        'add' =>['username','password'],
        'edit' =>['id','username'],
        'delete' =>['id'],
    ];
    // 自定义验证规则

}