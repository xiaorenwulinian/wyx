<?php

namespace app\admin\controller;

use app\admin\controller\AdminBase;
use think\Db;
use think\Exception;
use think\Request;

class Privilege extends AdminBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function lst()
    {
        $privilege = new \app\admin\model\Privilege();
        $parentData = $privilege->getTree();
        $this->setPageBtn('权限列表', '添加权限', url('add'));
        $this->assign([
            'parentData' => $parentData,
        ]);
        return $this->fetch();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $result = $this->validate($data, 'app\common\validate\Privilege.add');
            if (true !== $result) {
                $retArr = ['code' => 1, 'msg' => $result];
                return json($retArr);
            }
            try {
                $addRet = \app\admin\model\Privilege::create($data);
            } catch (Exception $e) {
                return json(['code' => 1, 'msg' => $e->getMessage()]);
            }
            return json(['code' => 0, 'msg' => '']);
        }
        $privilege = new \app\admin\model\Privilege();
        $parentData = $privilege->getTree();
        $this->setPageBtn('添加权限', '权限列表', url('lst?page=' . Input('get.page')));
        $this->assign(['parentData' => $parentData]);
        return $this->fetch();
    }

    public function edit()
    {
        $id = $this->request->param('id');
        if ($this->request->isPost()) {
            $allParams = $this->request->param();
            $result = $this->validate($allParams, 'app\common\validate\Privilege.edit');
            if (true !== $result) {
                return json(['code' => 1, 'msg' => $result]);
            }
            $privilege = new \app\admin\model\Privilege();

            $privilegeData = $privilege::find($id);
            if ($privilegeData) {
                $privilegeData->is_display = $allParams['is_display'];
                $privilegeData->parent_id = $allParams['parent_id'];
                $privilegeData->route_url = $allParams['route_url'];
                $privilegeData->pri_name = $allParams['pri_name'];
                try {
                    $privilegeData->save();
                } catch (\Exception $e) {
                    return json(['code' => 1, 'msg' => '修改失败']);
                }
                return json(['code' => 0, 'msg' => '']);
            }
            return json(['code' => 1, 'msg' => '非法入侵']);
        }
        $privilege = new \app\admin\model\Privilege();
        $data = $privilege->find($id);

        $parentData = $privilege->getTree();
        //修改后的权限不能是本身和子级
        $children = $privilege->getChildren($id);
        $this->assign([
            'data' => $data,
            'parentData' => $parentData,
            'children' => $children,
        ]);

        $this->setPageBtn('修改权限', '权限列表', url('lst?page=' . Input('get.page', 1)));
        return $this->fetch();
    }

    public function delete()
    {
        $id = $this->request->param('id');
        $allParams = $this->request->param();
        $result = $this->validate($allParams, 'app\common\validate\Privilege.delete');
        if (true !== $result) {
            return json(['code' => 1, 'msg' => $result]);
        }
        $privilege = new \app\admin\model\Privilege();
        $privilegeData = $privilege::find($id);
        if ($privilegeData) {
            Db::startTrans();
            try {
                // 先找出所有的子分类
                $hasChildren = $privilege->getChildren($id);
                if ($hasChildren) {
                    Db::table('privilege')->whereIn('id',$hasChildren)->update([
                        'is_del'=>1
                    ]);
                }
                Db::table('privilege')->where('id',$id)->update([
                    'is_del'=>1
                ]);
                Db::commit();
            } catch (Exception $exception) {
                Db::rollback();
                return json(['code' => 1, 'msg' => $exception->getMessage()]);
            }
            return json(['code' => 0, 'msg' => '']);
        }
        return json(['code' => 1, 'msg' => '非法入侵']);
    }
}
