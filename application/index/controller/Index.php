<?php

namespace app\index\controller;

use app\common\lib\ExcelUnit;
use app\common\lib\StringUnit;
use think\Controller;
use think\Db;

class Index extends Controller
{
    public function testExcel()
    {

        // 导出的数据集，二维数组
        $data = [
             ['id'=>1,'art_title'=>'aaa','add_time'=>'2019-05-11'],
             ['id'=>2,'art_title'=>'bbb','add_time'=>'2019-05-12'],
             ['id'=>3,'art_title'=>'ccc','add_time'=>'2019-05-13'],
             ['id'=>4,'art_title'=>'ddd','add_time'=>'2019-05-14'],
         ];
        //第一行标题头
        $header = [
            'id值','标题','添加时间'
        ];
        // 数组的每一列标题对应的字段
        $field = [
             'id', 'art_title','add_time'
         ];
        //导出的文件名称
        $file_name = '文章列表test';
        // 统一使用 app/common/lib/ExcelUnit  类库
        $excel =  ExcelUnit::getInstance();
        $excel->rawExcelExport($data,$header,$field,$file_name);
//        $excel->excelExport($data,$header,$field,$file_name);

    }
    public function testString() {
        $ret = StringUnit::randCode(4,true);
        dump($ret);
    }
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
