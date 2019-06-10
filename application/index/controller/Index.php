<?php

namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        $artData = Db::table('article')->order('id', 'desc')->paginate(20);
        $page = $artData->render();
        $typeData = Db::table('category')->where('parent_id','=',0)->select();
        $this->assign([
            'artData'=>$artData,
            'page'=>$page,
            'typeData'=>$typeData,
        ]);
        return $this->fetch();
    }

    public function type_art_lst()
    {
        $id = request()->param('type_id');
        $artData = Db::table('article')
            ->where('type_id', $id)
            ->order('id', 'desc')
            ->paginate(10);
        $page = $artData->render();

        $typeData = Db::table('category')->where('parent_id','=',0)->select();
        $this->assign('typeData', $typeData);
        $this->assign([
            'artData'=>$artData,
            'page'=>$page,
            'typeData'=>$typeData,
        ]);
        return $this->fetch();
    }

    public function artDetail()
    {
        $id = request()->param('art_id');
        $artData = Db::table('article')->where('id', $id)->find();
        $typeData = Db::table('category')->where('parent_id','=',0)->select();
        $next = Db::table('article')
            ->where(array(
                array('id', '<', $id),
//                        array('type_id','=',$artData['type_id'])
            ))
            ->find();
        $before = Db::table('article')
            ->where(array(
                array('id', '>', $id),
//                        array('type_id','=',$artData['type_id'])
            ))
            ->find();
        $this->assign([
            'artData'=>$artData,
            'typeData'=>$typeData,
            'before'=>$before,
            'next'=>$next,
        ]);
        return $this->fetch();
    }
}
