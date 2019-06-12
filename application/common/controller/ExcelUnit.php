<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019\6\12 0012
 * Time: 8:16
 */
namespace app\common\controller;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\Controller;

class ExcelUnit extends Controller
{
    /**
     * excel 导出
     * @param $data 导出的数据集，二维数组
     * @param $header 第一行标题头
     * @param $field 数组的每一列标题对应的字段
     * @param $file_name 导出的文件名称
     */
    public static function excelExport($data,$header,$field,$file_name)
    {
        /*
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
        */

        $spread_sheet = new Spreadsheet();  //创建一个新的excel文档
        $sheet = $spread_sheet->getActiveSheet(); //获取当前操作sheet的对象
//        $file_name = '文章列表';
//        $sheet->setTitle($file_name); //设置当前sheet的标题

        //设置header
        $i = 0;
        foreach ($header as $value) {
            $cellName = chr(65 + $i) . "1";
            $sheet->setCellValue($cellName, $value)->calculateColumnWidths();
            $sheet->getColumnDimension(chr(65 + $i))->setWidth('10');
            $i++;
        }
        //设置value
        $len = count($field);
        foreach ($data as $key => $item) {
//            $row = 2 + ($key * 2);
            $row = 2 + $key ;
            for ($i = 0; $i < $len; $i++) {
                $sheet->setCellValueExplicit(chr(65 + $i) . $row, $item[$field[$i]], DataType::TYPE_STRING);
            }
        }

        $writer = new Xlsx($spread_sheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器输出07Excel文件
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx"');//告诉浏览器输出浏览器名称
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        @ob_flush();
        exit;

    }
}