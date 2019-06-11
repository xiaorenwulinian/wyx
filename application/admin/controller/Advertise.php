<?php

namespace app\admin\controller;

use think\Db;
use app\admin\model\Advertise as advertiseModel;
use think\Exception;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\12\22 0022
 * Time: 14:55
 */
class Advertise extends AdminBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function lst()
    {
        //        $advertiseData = Db::name('advertise')->select();
        $default_page_size = 10; // 每一页默认显示的条数

        $where = [];
        $ad_title = input('ad_title');
        if ($ad_title) {
            $where[] = ['ad_title','like','%'. $ad_title . '%'];
        }
        $page_size = input('page_size',$default_page_size);
        $page_size_select = page_size_select($page_size); //生成下拉选择页数框
        $list = AdvertiseModel::where($where)->paginate($page_size,false,['query' => request()->param()]);
        $page = $list->render();
        $cur_page = input('page',1);
        $url = url('add',['page'=>$cur_page]);
        $this->setPageBtn('广告列表', '添加广告',$url) ;
        $this->assign([
            'list'=>$list,
            'page_show'=>$page,
            'page_size_select'=>$page_size_select,
            'page_size'=>$page_size,
            'page'=>$cur_page
        ]);
        return $this->fetch();
    }
    
    /**
     * 添加广告
     * @return mixed|\think\response\Json
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
           /*
            $result = $this->validate($data, 'app\common\validate\Advertise.add');
            if (true !== $result) {
                $retArr = ['code' => 1, 'msg' => $result];
                return json($retArr);
            }
           */
            try {
                advertiseModel::create($data);
            } catch (Exception $e) {
                return json(['code' => 1, 'msg' => $e->getMessage()]);
            }
            return json(['code' => 0, 'msg' => '']);
        }

        $this->setPageBtn('添加广告', '广告列表', url('lst?page=' . input('get.page')));
        $this->assign([
        ]);
        return $this->fetch();
    }

    /**
     * 添加时，上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('ad_img');
        $upload_second_dir = 'advertise'; //上传的二级目录
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->validate(['size'=>10000000])->move( "./uploads/{$upload_second_dir}/");
        if($info){

            //生成的文件路径
            $old_big_img = $big_file_path = str_replace('\\','/',$info->getSaveName());
            $thumb_img = str_replace($info->getFilename(),'thumb_'.$info->getFilename(),$old_big_img);
            $thumb_img_path = "./uploads/{$upload_second_dir}/".$thumb_img;
//            $image = \think\Image::open('./uploads/advertise/'.$big_file_path);
            $image = \think\Image::open(request()->file('ad_img'));
            $image->thumb(200, 200)->save($thumb_img_path);
            return  json([
                'code'=>0,
                'logo_file_path'=> $upload_second_dir.'/'.$big_file_path,
                'logo_file_path_thumb'=> $upload_second_dir.'/'.$thumb_img
            ]);
        }else{
            return  json(['code'=>1, 'msg'=> $file->getError()]);
        }
    }
    /**
     * 添加时，删除图片
     * @return \think\response\Json
     */
    public function deleteImg()
    {
        $cur_logo_path = $this->request->param('cur_logo_path');
        $cur_logo_path_thumb = $this->request->param('cur_logo_path_thumb');
        $uploads_path = env('root_path','../').'public/uploads/';
        if($cur_logo_path && $cur_logo_path_thumb) {
            if(file_exists($uploads_path.$cur_logo_path)) {
                unlink($uploads_path.$cur_logo_path);
            }
            if(file_exists($uploads_path.$cur_logo_path_thumb)) {
                unlink($uploads_path.$cur_logo_path_thumb);
            }
            return  json(['code'=>0, 'msg'=> '']);
        }
        return  json(['code'=>1, 'msg'=> '删除失败']);
    }
    
    
    public function edit()
    {
        $id = $this->request->param('id');
        $cur_page = input('page');

        if ($this->request->isPost()) {
            $allParams = $this->request->param();
            /*
            $result = $this->validate($allParams, 'app\admin\validate\Advertise.edit');
            if (true !== $result) return json(['code' => 1, 'msg' => $result]);
            */
            $advertise = advertiseModel::find($id);
            if ($advertise) {
                try {
                    /*
                    id : ad_id,
                    ad_title : ad_title,
                    ad_url : ad_url,
                    ad_content : ad_content,
                    is_display : is_display,
                    order_id : order_id,
                    */
                    advertiseModel::update($allParams);
                } catch (\Exception $e) {
                    return json(['code' => 1, 'msg' => '修改失败']);
                }
                return json(['code' => 0, 'msg' => '']);
            }
            return json(['code' => 1, 'msg' => '非法入侵']);
        }

        $advertiseData = Db::name('advertise')->find($id);
        $this->assign([
            'advertiseData' => $advertiseData,
        ]);
        $url = url('lst');
        $this->setPageBtn('修改广告', '广告列表',$url );
        return $this->fetch();
    }

    /**
     * 修改时，上传商品单图片
     * @return \think\response\Json
     */
    public function editUploadPic(){
        $ad_id = $this->request->param('ad_id');
        $upload_path_root_dir =  'uploads';
        $upload_path_second_dir =  'advertise'; //上传的二级目录
        $upload_path_prefix_dir = './' . $upload_path_root_dir . '/' . $upload_path_second_dir . '/';
        $advertise = advertiseModel::find($ad_id);
        if ($advertise) {
            $file = request()->file('ad_img');
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $file->validate(['size'=>10000000])->move( $upload_path_prefix_dir);
            if($info){
                //生成的文件路径
                $old_big_img = $big_file_dir = str_replace('\\','/',$info->getSaveName());
                $thumb_img = str_replace($info->getFilename(),'thumb_'.$info->getFilename(),$old_big_img);
                $thumb_img_path = $upload_path_prefix_dir.$thumb_img;
                $image = \think\Image::open($upload_path_prefix_dir.$big_file_dir);
                $image->thumb(200, 200)->save($thumb_img_path); // 生成缩略图，只有图片可以生成缩略图，文本视频不行
                $advertise->ad_img = $upload_path_second_dir.'/'.$big_file_dir;
                $advertise->ad_thumb_img = $upload_path_second_dir.'/'.$thumb_img;
                try{
                    $advertise->save();
                }catch (\Exception $e) {
                    return  json(['code'=>1, 'msg'=>'上传图片失败']);
                }
                return  json([
                    'code'=>0,
                    'logo_file_path'=> $upload_path_second_dir.'/'.$big_file_dir,
                    'logo_file_path_thumb'=> $upload_path_second_dir.'/'.$thumb_img
                ]);
            }else{
                return  json(['code'=>1, 'msg'=> $file->getError()]);
            }
        }
        return  json(['code'=>1, 'msg'=> '非法上传']);
    }

    /**
     * 修改时，删除商品单图片
     * @return mixed|\think\response\Json
     *
     */
    public function updateDelImg(){
        $id = $this->request->param('id');
        $advertise = advertiseModel::find($id);
        if ($advertise) {
            $logo_path = $advertise->ad_img;
            $sm_logo_path = $advertise->ad_thumb_img;
            $uploads_path = env('root_path','../').'public/uploads/';
            //删除数据库文件内容
            $advertise->ad_img = '';
            $advertise->ad_thumb_img = '';
            try {
                $advertise->save();
            }catch (\Exception $e) {
                return  json(['code'=>1, 'msg'=> '删除失败']);
            }
            // 删除本地的资源文件
            if (file_exists($uploads_path.$logo_path)) {
                unlink($uploads_path.$logo_path);
            }
            if (file_exists($uploads_path.$sm_logo_path)) {
                unlink($uploads_path.$sm_logo_path);
            }
            return  json(['code'=>0, 'msg'=> '']);
        }
        return  json(['code'=>1, 'msg'=> '删除失败']);
    }

    public function delete(){
        $id = $this->request->param('id');
        $result = $this->validate(['id'=>$id],'app\common\validate\advertise.delete');
        if(true !== $result){
            return  json(['code'=>1, 'msg'=>$result]);
        }
        $advertise = advertiseModel::find($id);
        if($advertise) {
            try{
                advertiseModel::destroy($id);
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

    /**
     *
     * 显示详细信息，只读
     */
    public function detailInfoById()
    {
        $id = $this->request->param('id');

        $advertiseData = Db::name('advertise')->find($id);
        $this->assign([
            'advertiseData' => $advertiseData,
        ]);

        $url = url('lst');
        $this->setPageBtn('修改广告', '广告列表',$url );
        return $this->fetch();
    }
}