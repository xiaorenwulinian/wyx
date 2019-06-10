<?php

namespace app\admin\controller;

use think\Db;
use app\admin\model\Role as RoleModel;
use app\admin\model\Privilege as PrivilegeModel;
use think\Debug;
use think\Exception;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\12\22 0022
 * Time: 14:55
 */
class Role extends AdminBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function lst()
    {
        $roleData = Db::name('role')->select();
        $this->setPageBtn('角色列表', '添加角色', url('add'));
        $this->assign([
            'roleData' => $roleData,
        ]);
        return $this->fetch();
    }

    /**
     * 添加角色
     * @return mixed|\think\response\Json
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $result = $this->validate($data, 'app\common\validate\Role.add');
            if (true !== $result) {
                $retArr = ['code' => 1, 'msg' => $result];
                return json($retArr);
            }
            try {
                RoleModel::create($data);
            } catch (Exception $e) {
                return json(['code' => 1, 'msg' => $e->getMessage()]);
            }
            return json(['code' => 0, 'msg' => '']);
        }

        $privilegeData = DB::name('privilege')->select();
        $priData = $this->getTree($privilegeData);
        $this->setPageBtn('添加角色', '角色列表', url('lst?page=' . Input('get.page')));
        $this->assign([
            'priData' => $priData,
        ]);
        return $this->fetch();
    }

    /**
     * 递归获取所有权限以及级别
     * @param $data
     * @param int $parent_id
     * @param int $level
     * @param bool $isClear
     * @return array
     */
    private function getTree($data, $parent_id = 0, $level = 0, $isClear = TRUE)
    {
        static $ret = array();
        if ($isClear)
            $ret = array();
        foreach ($data as $k => $v) {
            if ($v['parent_id'] == $parent_id) {
                $v['level'] = $level;
                $ret[] = $v;
                $this->getTree($data, $v['id'], $level + 1, FALSE);
            }
        }
        return $ret;
    }

    public function edit()
    {
        $id = $this->request->param('id');

        if ($this->request->isPost()) {
            $allParams = $this->request->param();
            $result = $this->validate($allParams, 'app\admin\validate\Role.edit');
            if (true !== $result) return json(['code' => 1, 'msg' => $result]);
            $Role = RoleModel::find($id);
            if ($Role) {


                try {
                    RoleModel::update(['id' => $id, 'role_name' => $allParams['role_name']]);
//                    $Role->role_name     = $allParams['role_name'];
//                    $Role->save();
                } catch (\Exception $e) {
                    return json(['code' => 1, 'msg' => '修改失败']);
                }
                return json(['code' => 0, 'msg' => '']);
            }
            return json(['code' => 1, 'msg' => '非法入侵']);
        }
        $roleData = Db::name('role')->find($id);
        $privilegeData = DB::name('privilege')->select();
        $priData = $this->getTree($privilegeData);

        $priIdsData = Db::name('role_privilege')->where('role_id', $id)->column('pri_id');

        $this->assign([
            'priData' => $priData,
            'roleData' => $roleData,
            'priIdsData' => $priIdsData,
        ]);
        $this->setPageBtn('修改角色', '角色列表', url('lst'));
        return $this->fetch();
    }


    public function delete(){
        $id = $this->request->param('id');
        $result = $this->validate(['id'=>$id],'app\common\validate\Role.delete');
        if(true !== $result){
            return  json(['code'=>1, 'msg'=>$result]);
        }
        $Role = RoleModel::find($id);
        if($Role) {
            try{
                RoleModel::destroy($id);
            }catch (\Exception $e) {
                return  json(['code'=>1, 'msg'=>'删除失败!']);
            }
            return  json(['code'=>0, 'msg'=>'']);
        }
        return  json(['code'=>1, 'msg'=>'非法入侵']);
    }


    public function edit_sort()
    {
        $id = $this->request->param('id');
        $sort_id = $this->request->param('sort_id');
        $ret = Db::name('Admin')
            ->where('id', '=', $id)
            ->update(['sort_id' => $sort_id]);
        if ($ret) {
            return json(['code' => 0, 'msg' => '']);
        }
        return json(['code' => 1, 'msg' => '修改失败，请稍后再试!']);

    }

    public function modifyPassword()
    {
        $admin_id = session('admin_user_id');
        $admin_data = Db::table('admin')->find($admin_id);
        if ($this->request->isPost()) {
            $pwd = $this->request->param('password');
            $new_pwd = md5($pwd);
            try {
                Db::table('admin')->where('id', $admin_id)->update([
                    'password' => $new_pwd
                ]);
            } catch (Exception $e) {
                return json(['code' => 1, 'msg' => $e->getMessage()]);
            }
            return json(['code' => 0, 'msg' => '']);
        }

        $this->assign([
            'admin_data' => $admin_data
        ]);
        return $this->fetch();
    }
}