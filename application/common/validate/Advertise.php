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
        'ad_title'  =>  'require|max:2550',
        'ad_content'  =>  'require',
        'ad_img'  =>  'require',
        'ad_thumb_img' =>  'require',
        'ad_url'  =>  'require|max:255',
        'order_id'  =>  'require|number',
        'is_display'  =>  'require|number',
        'add_time'  =>  'require|number',
        'edit_time'  =>  'require|number',
        'id' =>  'require|number',
    ];

    protected $message = [
        'ad_title.require'  =>  '标题必须填写',
        'ad_title.max' =>  '不能超过255个字',
        'ad_content.require'  =>  '内容详情必须填写',
        'ad_img.require'  =>  '内容详情必须填写',
        'ad_img.max' =>  '不能超过255个字',
        'ad_url.require'  =>  '跳转链接必须填写',
        'ad_url.max' =>  '跳转链接不能超过255个字',
    ];

    //使用场景
    protected $scene = [
        'add' =>['ad_title','ad_content'],
        'edit' =>['id','ad_title','ad_content'],
        'delete' =>['id'],
    ];
    // 自定义验证规则

}