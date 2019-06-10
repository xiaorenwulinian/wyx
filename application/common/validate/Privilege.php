<?php
namespace app\common\validate;
use think\Validate;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\12\22 0022
 * Time: 16:29
 */
class Privilege extends Validate {
    //验证规则
    protected $rule = [
        'id'  =>  'require|number',
        'pri_name'  =>  'require|max:60',
        'route_url'  =>  'require|max:60',
        'pri_icon'  =>  'require|max:60',
        'parent_id'  =>  'require|number',
        'is_display'  =>  'require|max:60',
        'is_del'  =>  'require|number',
    ];

    protected $message = [
        'pri_name.require'  =>  '权限名称必须填写',
        'pri_name.max'  =>  '权限名称小于60个字',
        'route_url.require'  =>  '路由地址必须填写',
        'route_url.max'  =>  '路由地址小于60个字',
    ];

    //使用场景
    protected $scene = [
        'add' =>[
            'pri_name','route_url'
        ],
        'edit' =>['id','pri_name','route_url'],
        'delete' =>['id'],
    ];
    // 自定义验证规则

}