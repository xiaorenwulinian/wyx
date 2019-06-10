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

}
