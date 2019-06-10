<?php

namespace app\admin\model;

use think\Db;
use think\Model;

class Admin extends Model
{
    protected $autoWriteTimestamp = false;

    protected static function init(){

        self::beforeInsert(function ($data) {
        });
        self::afterInsert(function ($data) {
            $role_ids =  input('role_id');
            if (!empty($role_ids)) {
                $insert_arr = [];
                foreach ($role_ids as $role_id) {
                    $insert_arr[]  = [
                        'role_id'=>$role_id,
                        'admin_id'=>$data->id
                    ];
                }
                Db::name('admin_role')->insertAll($insert_arr);
            }
        });

        self::afterUpdate(function ($data) {

            $role_ids = input('role_id');
            $admin_id = $data->id;
            Db::name('admin_role')->where('admin_id','=',$admin_id)->delete();
            if (!empty($role_ids)) {
                $insert_arr = [];
                foreach ($role_ids as $role_id) {
                    $insert_arr[]  = [
                        'role_id'=>$role_id,
                        'admin_id'=>$admin_id
                    ];
                }
                Db::name('admin_role')->insertAll($insert_arr);
            }

        });
        self::beforeDelete(function ($data) {
            DB::table('role_privilege')->where('role_id',$data->id)->delete();
        });
    }

}
