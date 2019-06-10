<?php

namespace app\admin\controller;

use app\admin\controller\AdminBase;
use think\Db;
use think\Exception;
use think\Request;

class Category extends AdminBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function lst()
    {
        $privilege = new \app\admin\model\Category();
        $cateData = $privilege->getTree();
//        $cateData = Db::name('category')->select();

        $this->setPageBtn('文章分类列表', '添加文章分类', url('add'));
        $this->assign([
            'cateData' => $cateData,
        ]);
        return $this->fetch();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $result = $this->validate($data, 'app\common\validate\Category.add');
            if (true !== $result) {
                $retArr = ['code' => 1, 'msg' => $result];
                return json($retArr);
            }
            try {
                $addRet = \app\admin\model\Category::create($data);
            } catch (Exception $e) {
                return json(['code' => 1, 'msg' => $e->getMessage()]);
            }
            return json(['code' => 0, 'msg' => '']);
        }
        $category = new \app\admin\model\Category();
        $parentData = $category->getTree();
//        $parentData = Db::name('category')->select();
        $this->setPageBtn('添加文章分类', '文章分类列表', url('lst?page=' . Input('get.page')));
        $this->assign(['parentData' => $parentData]);
        return $this->fetch();
    }

    public function edit()
    {
        $id = $this->request->param('id');
        if ($this->request->isPost()) {
            $allParams = $this->request->param();
            $result = $this->validate($allParams, 'app\common\validate\Category.edit');
            if (true !== $result) {
                return json(['code' => 1, 'msg' => $result]);
            }
            $privilege = new \app\admin\model\Category();

            $privilegeData = $privilege::find($id);
            if ($privilegeData) {
                $privilegeData->parent_id = $allParams['parent_id'];
                $privilegeData->title = $allParams['title'];
                try {
                    $privilegeData->save();
                } catch (\Exception $e) {
                    return json(['code' => 1, 'msg' => '修改失败']);
                }
                return json(['code' => 0, 'msg' => '']);
            }
            return json(['code' => 1, 'msg' => '非法入侵']);
        }
        $privilege = new \app\admin\model\Category();
        $data = $privilege->find($id);

        $parentData = $privilege->getTree();
        //修改后的文章分类不能是本身和子级
        $children = $privilege->getChildren($id);
        $this->assign([
            'data' => $data,
            'parentData' => $parentData,
            'children' => $children,
        ]);

        $this->setPageBtn('修改文章分类', '文章分类列表', url('lst?page=' . Input('get.page', 1)));
        return $this->fetch();
    }

    public function delete()
    {
        $id = $this->request->param('id');
        $allParams = $this->request->param();
        $result = $this->validate($allParams, 'app\common\validate\Category.delete');
        if (true !== $result) {
            return json(['code' => 1, 'msg' => $result]);
        }
        $privilege = new \app\admin\model\Category();
        $privilegeData = $privilege::find($id);
        if ($privilegeData) {
            Db::startTrans();
            try {
                // 先找出所有的子分类
                $hasChildren = $privilege->getChildren($id);
                if ($hasChildren) {
                    Db::name('privilege')->whereIn('id',$hasChildren)->update([
                        'is_del'=>1
                    ]);
                }
                Db::name('privilege')->where('id',$id)->update([
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
