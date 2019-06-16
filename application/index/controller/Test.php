<?php

namespace app\index\controller;

use app\common\lib\ExcelUnit;
use app\common\lib\MailUnit;
use app\common\lib\StringUnit;
use think\Controller;
use think\Db;
use think\facade\Env;
use think\facade\Request;

class Test extends Controller
{
    /**
     * excel 导入
     */
    public function testExcelImport()
    {
        // Env::get('root_path') 获取项目根路径
        $uploadfile = Env::get('root_path').'public/excel001.xlsx';
        $excel = ExcelUnit::getInstance();
        $data = $excel->excelImport($uploadfile);
        dump($data);
        return ;
    }

    /**
     * Excel 导出
     */
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
//        $excel->excelExport($data,$header,$field,$file_name); // 第三方封装
        $excel->rawExcelExport($data,$header,$field,$file_name); // php 原生


    }

    public function testString() {
        $ret = StringUnit::randCode(4,true);
        dump($ret);
    }

    /**
     * 测试邮件发送
     */
    public function testmail()
    {
        $to = '2806044627@qq.com';  //收件人
        $subject = 'test mail send';  // 标题
        $message = 'the content can not empty'; // 内容

        $mail = MailUnit::sendMail($to,$subject,$message);
        dump($mail);
    }



}
