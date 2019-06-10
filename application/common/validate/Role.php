<?php
namespace app\common\validate;

use think\Validate;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\12\22 0022
 * Time: 16:29
 */
class Role extends Validate {
    //验证规则
    protected $rule = [
        'role_name'  =>  'require|max:60',
        'id' =>  'require|number',
    ];

    protected $message = [
        'role_name.require'  =>  '标题必须填写',
        'title.max' =>  '不能超过60个字',
    ];

    //使用场景
    protected $scene = [
        'add' =>['role_name'],
        'edit' =>['id','role_name'],
        'delete' =>['id'],
    ];
    // 自定义验证规则

}