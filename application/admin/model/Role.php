<?php

namespace app\admin\model;

use think\Db;
use think\Model;

class Role extends Model
{

    protected $autoWriteTimestamp = false;

    protected static function init(){

        self::beforeInsert(function ($data) {
        });
        self::afterInsert(function ($data) {
            $pri_ids =  input('pri_id');
            if (!empty($pri_ids)) {
                foreach ($pri_ids as $pri_id) {
                    Db::name('role_privilege')->insert([
                        'pri_id'=>$pri_id,
                        'role_id'=>$data->id
                    ]);
                }
            }
        });
        self::afterUpdate(function ($data) {
            $pri_ids =  input('pri_id');
            DB::table('role_privilege')->where('role_id',$data->id)->delete();
            if (!empty($pri_ids)) {
                foreach ($pri_ids as $pri_id) {
                    Db::table('role_privilege')->insert([
                        'pri_id'=>$pri_id,
                        'role_id'=>$data->id
                    ]);
                }
            }
        });
        self::beforeDelete(function ($data) {
            DB::table('role_privilege')->where('role_id',$data->id)->delete();
        });
    }

}
