<?php
namespace app\admin\controller;
use think\Db;
use app\admin\model\Admin as AdminModel;
use think\Debug;
use think\Exception;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\12\22 0022
 * Time: 14:55
 */
class Admin extends AdminBase {
    public function __construct(){
        parent::__construct();
    }
    /*
     *  管理员列表
     */
    public function lst(){
        $list = AdminModel::paginate(5);
        $page = $list->render();
        $cur_page = input('page') ? input('page') : 1 ;
        $this->setPageBtn('管理员列表', '添加管理员', url('add',['page'=>$cur_page]));
        $this->assign([
            'list'=>$list,
            'page_show'=>$page,
            'page'=>$cur_page
        ]);
        return $this->fetch();
    }

    public function add(){
        if($this->request->isPost()) {
            $data = $this->request->param();
            $result = $this->validate($data,'app\common\validate\Admin.add');
            if(true !== $result){
                $retArr = ['code'=>1, 'msg'=>$result];
                return  json($retArr);
            }
            try{
                $addRet = AdminModel::create($data);
            }catch (Exception $e){
                return  json(['code'=>1, 'msg'=>$e->getMessage()]);
            }
            return  json(['code'=>0, 'msg'=>'']);
        }
        $roleData = Db::name('role')->select();
        $adminData = Db::name('admin')->select();
        $this->setPageBtn('添加管理员', '管理员列表', url('lst?page='.Input('get.page')));
        $this->assign([
            'roleData'=>$roleData,
            'adminData'=>$adminData,
        ]);
        return $this->fetch();
    }
    public function edit(){

        $id = $this->request->param('id');

        if($this->request->isPost()) {
            $allParams = $this->request->param();
            $result = $this->validate($allParams,'app\common\validate\Admin.edit');
            if(true !== $result){
                $retArr = ['code'=>1, 'msg'=>$result];
                return  json($retArr);
            }
            $Admin = AdminModel::find($id);
            if($Admin) {
                try{
                    /*
                    $Admin->username = $allParams['username'];
                    if (!empty($allParams['password'])) {
                        $Admin->password = $allParams['password'];
                    }
                     $ret = $Admin->save();
                    */
                    $update_where = [];
                    $update_where['id'] = $id;
                    $update_where['username'] = $allParams['username'];
                    if (!empty($allParams['password'])) {
                        $update_where['password'] = md5($allParams['password']);
                    }
                     AdminModel::update($update_where);
                }catch (\Exception $e) {
                    return  json(['code'=>1, 'msg'=>'修改失败']);
                }
                return  json(['code'=>0, 'msg'=>'']);
            }
            return  json(['code'=>1, 'msg'=>'非法入侵']);
        }
        $admin_data = Db::name('admin')->find($id);
        $role_data = Db::name('role')->select();
        $role_id_arr = Db::name('admin_role')->where('admin_id','=',$id)->column('role_id');
        $this->assign([
            'admin_data'=>$admin_data,
            'role_data'=>$role_data,
            'role_id_arr'=>$role_id_arr,
        ]);
        $this->setPageBtn('修改管理员', '管理员列表', url('lst?p='.input('get.p')));
        return $this->fetch();
    }
    public function delete(){
        $id = $this->request->param('id');
        $result = $this->validate(['id'=>$id],'app\admin\validate\Admin.delete');
        if(true !== $result){
            return  json(['code'=>1, 'msg'=>$result]);
        }
        $Admin = AdminModel::get($id);
        if($Admin) {
            $Admin->is_del = 1;
            try{
                $Admin->save();
            }catch (\Exception $e) {
                return  json(['code'=>1, 'msg'=>'删除失败!']);
            }
            return  json(['code'=>0, 'msg'=>'']);
        }
        return  json(['code'=>1, 'msg'=>'非法入侵']);
     
    }

    /**
     * 修改管理员是否禁用
     */
    public function ajaxEditAdminUse()
    {
        $id = $this->request->param('id');
        if ($id == 1) {
            return  json(['code'=>1, 'msg'=>'管理员不能禁用!']);
        }
        $admin = Db::name('admin')->find($id);
        if (!empty($admin)) {
            try {
                $is_use = $admin['is_use'] == 0 ? 1 : 0;
                Db::name('admin')->where('id','=',$id)->update(['is_use'=>$is_use]);
            } catch (Exception $e) {
                return  json(['code'=>1, 'msg'=>$e->getMessage()]);
            }
            return  json(['code'=>0, 'msg'=>'setting success']);
        }
        return  json(['code'=>1, 'msg'=>'没有该管理员']);
    }


    public function edit_sort(){
        $id = $this->request->param('id');
        $sort_id = $this->request->param('sort_id');
        $ret = Db::name('Admin')
            ->where('id','=',$id)
            ->update(['sort_id'=>$sort_id]);
        if($ret) {
            return  json(['code'=>0, 'msg'=>'']);
        }
        return  json(['code'=>1, 'msg'=>'修改失败，请稍后再试!']);

    }

    public function modifyPassword()
    {
        $admin_id  = session('admin_user_id');
        $admin_data = Db::table('admin')->find($admin_id);
        if($this->request->isPost()) {
            $pwd = $this->request->param('password');
            $new_pwd = md5($pwd);
            try {
                Db::table('admin')->where('id',$admin_id)->update([
                    'password'=>$new_pwd
                ]);
            } catch (Exception $e) {
                return  json(['code'=>1, 'msg'=>$e->getMessage()]);
            }
            return  json(['code'=>0, 'msg'=>'']);
        }

        $this->assign([
            'admin_data'=>$admin_data
        ]);
        return $this->fetch();
    }
}