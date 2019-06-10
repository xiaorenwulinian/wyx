<?php
namespace app\common\validate;
use think\Validate;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\12\22 0022
 * Time: 16:29
 */
class Category extends Validate {
    //验证规则
    protected $rule = [
        'id'  =>  'require|number',
        'title'  =>  'require|max:60',
        'parent_id'  =>  'require|number',
    ];

    protected $message = [
        'title.require'  =>  '权限名称必须填写',
        'title.max'  =>  '权限名称小于60个字',
    ];

    //使用场景
    protected $scene = [
        'add' =>['title'],
        'edit' =>['id','pri_name','route_url'],
        'delete' =>['id'],
    ];
    // 自定义验证规则

}