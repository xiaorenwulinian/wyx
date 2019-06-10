<?php

namespace app\admin\controller;

use think\Db;
use think\Request;

class Index extends AdminBase
{
    public function index(){

        /********** 取出当前管理员所拥有的前两级的权限 ************/
        // 取出当前管理员所有的权限
        $adminId = session('admin_user_id');
        if($adminId == 1){
            $sql = 'SELECT * FROM privilege WHERE is_display=0';
        }
        else{
            $sql = 'SELECT b.*
			  FROM role_privilege a
			   LEFT JOIN privilege b ON a.pri_id=b.id
			   LEFT JOIN admin_role c ON a.role_id=c.role_id
			    WHERE c.admin_id='.$adminId;
        }
        $pri = Db::query($sql);
        $allMenu = [];  // 放前两级的权限
        // 从所有的权限中取出前两级的权限
        /*
        foreach ($pri as $k => $v) {
            // 找顶级权限
            if($v['parent_id'] == 0) {
                // 再循环把这个顶级权限的子权限
                foreach ($pri as $k1 => $v1) {
                    if($v1['parent_id'] == $v['id']) {
                        $v['children'][] = $v1;
                    }
                }
                $allMenu[] = $v;
            }
        }
        */
        foreach ($pri as $k=>$v) {
            if ($v['parent_id'] == 0) {
                foreach ($pri as $k1=>$v1) {
                    if ($v1['parent_id'] == $v['id']) {
                        foreach ($pri as $k2=>$v2) {
                            if ($v2['parent_id'] == $v1['id']) {
                                $v1['children'][] = $v2;
                            }
                        }
                        $v['children'][] = $v1;
                    }
                }
                $allMenu[] = $v;
            }
        }

//        $arr1 = $allMenu[0]['children'][0];

        $this->assign(['allMenu'=>$allMenu]);

        return $this->fetch();
    }

    public function mainContent() {
        return $this->fetch();
    }
}
