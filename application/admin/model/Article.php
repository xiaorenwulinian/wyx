<?php

namespace app\admin\model;

use think\Db;
use think\Model;

class Article extends Model
{

    protected $autoWriteTimestamp = false;

    protected static function init(){

        self::beforeInsert(function ($data) {
        });
        self::afterInsert(function ($data) {

        });
        self::afterUpdate(function ($data) {

        });
        self::beforeDelete(function ($data) {
        });
    }

    /**
     * 后台文章筛选
     * @param $page_size 每页显示的数量
     */
    public function search($page_size)
    {

        $where = [];
        $art_title = input('art_title');
        if ($art_title) {
            $where[] = ['a.art_title','like','%'. $art_title . '%'];
        }
        $type_id = input('type_id');
        if ($type_id) {
            $where[] = ['a.type_id','=', $type_id];
        }
        $begin_time = input('begin_time');
        $end_time = input('end_time');
        if ($begin_time && $end_time) {
            //开始时间 大于 结束时间 ，特殊情况 可以不处理，或者将两个时间替换
            if (($end_time) < $begin_time) {
                list($end_time,$begin_time) = [$begin_time,$end_time];
            }
            $begin_time_int = strtotime(date('Y-m-d',strtotime($begin_time)) . ' 00:00:00');
            $end_time_int = strtotime(date('Y-m-d',strtotime($end_time)) . ' 23:59:59');
            $where[] = ['a.add_time','between',[$begin_time_int,$end_time_int]];
        } elseif ($begin_time) {
            //只有开始时间
            $begin_time_int = strtotime(date('Y-m-d',strtotime($begin_time)) . ' 00:00:00');
            $where[] = ['a.add_time','>=',$begin_time_int];
        } elseif ($end_time) {
            // 只有结束时间
            $end_time_int = strtotime(date('Y-m-d',strtotime($end_time)) . ' 23:59:59');
            $where[] = ['a.add_time','<=',$end_time_int];
        }


        $select_filed = [
            'a.id','a.art_title','a.art_desc','a.type_id','c.title AS cat_title','a.add_time'
        ];
        $list = self::alias('a')
            ->leftJoin('category c','a.type_id = c.id')
            ->where($where)
            ->field($select_filed)
            ->order('a.id','desc')
            ->paginate($page_size,false,['query' => request()->param()]);
        return $list;
    }

}
