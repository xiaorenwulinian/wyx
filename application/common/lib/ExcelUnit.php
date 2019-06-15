<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019\6\12 0012
 * Time: 8:16
 */
namespace app\common\lib;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelUnit
{
    protected static $instance ;
    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * excel 导出
     * @param $data 导出的数据集，二维数组
     * @param $header 第一行标题头
     * @param $field 数组的每一列标题对应的字段
     * @param $file_name 导出的文件名称
     */
    public function excelExport($data,$header,$field,$file_name)
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

    /**
     * 原生　php excel 导出
     * @param $data 导出的数据集，二维数组
     * @param $header 第一行标题头
     * @param $field 数组的每一列标题对应的字段
     * @param $file_name 导出的文件名称
     */
    public function rawExcelExport($data,$header,$field,$file_name)
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
            'id值','添加时间','标题'
        ];
        // 数组的每一列标题对应的字段
        $field = [
            'id','add_time', 'art_title'
        ];
        //导出的文件名称
        $file_name = '文章列表'.date('Y-m-d-H_i_s');
        */
        $table = "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />";
        $table .= "<table  border='1'>";
        // 标题头部信息
        $table .= "<tr>";
        foreach ($header as $header_name) {
            $table .= "<td>{$header_name}</td>";
        }
        $table .= "</tr>";
        // 渲染数据
        foreach ($data as $cur_data) {
            $table .= "<tr>";
            foreach ($field as $k=>$field_value) {
                $table .= "<td>{$cur_data[$field_value]}</td>";
            }
            $table .= "</tr>";
        }
        $table .= "</table>";
        header("Pragma: public");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition: inline;filename={$file_name}.xls");

        echo $table;
        die;
    }


}