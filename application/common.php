<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

//生成页码跳转
function page_size_select($page_size=0){
    $str  = '<select class="form-control page_size_select">';
    $all_page  = [
        5,10,20,50,100
    ];
    foreach ($all_page as $cur_page) {
        $has_selected = $page_size == $cur_page ? "selected='selected'" : '';
        $str .= "<option value='{$cur_page}' {$has_selected}>{$cur_page}条/页</option>";
    }
    $str .= '</select>';
    return $str;
}