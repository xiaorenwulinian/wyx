<?php
namespace app\common\validate;

use think\Validate;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\12\22 0022
 * Time: 16:29
 */
class Article extends Validate {
    //验证规则
    protected $rule = [
        'art_title'  =>  'require|max:60',
        'art_desc'  =>  'require|max:255',
        'art_content'  =>  'require',
        'type_id'  =>  'require|number',
        'order_id'  =>  'require|number',
        'add_time'  =>  'require|number',
        'edit_time'  =>  'require|number',
        'id' =>  'require|number',
    ];

    protected $message = [
        'art_title.require'  =>  '标题必须填写',
        'art_title.max' =>  '不能超过60个字',
    ];

    //使用场景
    protected $scene = [
        'add' =>['art_title','art_content'],
        'edit' =>['id','art_title','art_content'],
        'delete' =>['id'],
    ];
    // 自定义验证规则

}