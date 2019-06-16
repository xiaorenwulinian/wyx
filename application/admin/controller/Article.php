<?php

namespace app\admin\controller;

use think\Db;
use app\admin\model\Article as ArticleModel;
use think\Exception;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\12\22 0022
 * Time: 14:55
 */
class Article extends AdminBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function lst()
    {
        //        $articleData = Db::name('article')->select();
        $default_page_size = 10; // 每一页默认显示的条数
        $where = [];
        $art_title = input('art_title');
        if ($art_title) {
            $where[] = ['a.art_title','like','%'. $art_title . '%'];
        }
        $type_id = input('type_id');
        if ($type_id) {
            $where[] = ['a.type_id','=', $type_id];
        }
        $begin_time = input('begin_time');
        $end_time = input('end_time');
        if ($begin_time && $end_time) {
            //开始时间 大于 结束时间 ，特殊情况 可以不处理，或者将两个时间替换
            if (($end_time) < $begin_time) {
                list($end_time,$begin_time) = [$begin_time,$end_time];
            }
            $begin_time_int = strtotime(date('Y-m-d',strtotime($begin_time)) . ' 00:00:00');
            $end_time_int = strtotime(date('Y-m-d',strtotime($end_time)) . ' 23:59:59');
            $where[] = ['a.add_time','between',[$begin_time_int,$end_time_int]];
        } elseif ($begin_time) {
            //只有开始时间
            $begin_time_int = strtotime(date('Y-m-d',strtotime($begin_time)) . ' 00:00:00');
            $where[] = ['a.add_time','>=',$begin_time_int];
        } elseif ($end_time) {
            // 只有结束时间
            $end_time_int = strtotime(date('Y-m-d',strtotime($end_time)) . ' 23:59:59');
            $where[] = ['a.add_time','<=',$end_time_int];
        }

        $page_size = input('page_size',$default_page_size);
        $page_size_select = page_size_select($page_size); //生成下拉选择页数框
        $cat_data = Db::name('category')->select();
        $select_filed = [
            'a.id','a.art_title','a.art_desc','a.type_id','c.title AS cat_title','a.add_time'
        ];
        $list = ArticleModel::alias('a')
            ->leftJoin('category c','a.type_id = c.id')
            ->where($where)
            ->field($select_filed)
            ->order('a.id','desc')
            ->paginate($page_size,false,['query' => request()->param()]);
        $page = $list->render();
        $cur_page = input('page',1);
        $url = url('add',['page'=>$cur_page]);
        $this->setPageBtn('文章列表', '添加文章',$url) ;
        $this->assign([
            'cat_data'=>$cat_data,
            'list'=>$list,
            'page_show'=>$page,
            'page_size_select'=>$page_size_select,
            'page_size'=>$page_size,
            'page'=>$cur_page
        ]);
        return $this->fetch();
    }

    /**
     * 添加文章
     * @return mixed|\think\response\Json
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $result = $this->validate($data, 'app\common\validate\Article.add');
            if (true !== $result) {
                $retArr = ['code' => 1, 'msg' => $result];
                return json($retArr);
            }
            try {
                articleModel::create($data);
            } catch (Exception $e) {
                return json(['code' => 1, 'msg' => $e->getMessage()]);
            }
            return json(['code' => 0, 'msg' => '']);
        }

        $cateData = DB::name('category')->select();
        $cateData = $this->getTree($cateData);
        $this->setPageBtn('添加文章', '文章列表', url('lst?page=' . input('get.page')));
        $this->assign([
            'priData' => $cateData,
        ]);
        return $this->fetch();
    }

    /**
     * 递归获取所有分类以及级别
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
        $cur_page = input('page');

        if ($this->request->isPost()) {
            $allParams = $this->request->param();
            $result = $this->validate($allParams, 'app\admin\validate\Article.edit');
            if (true !== $result) return json(['code' => 1, 'msg' => $result]);
            $article = articleModel::find($id);
            if ($article) {
                try {
                    articleModel::update($allParams);
//                    $article->article_name     = $allParams['article_name'];
//                    $article->save();
                } catch (\Exception $e) {
                    return json(['code' => 1, 'msg' => '修改失败']);
                }
                return json(['code' => 0, 'msg' => '']);
            }
            return json(['code' => 1, 'msg' => '非法入侵']);
        }

        $articleData = Db::name('article')->find($id);
        $cateData = DB::name('category')->select();
        $cateData = $this->getTree($cateData);
        $this->assign([
            'articleData' => $articleData,
            'cateData' => $cateData,
        ]);
        $url = url('lst');
        $this->setPageBtn('修改文章', '文章列表',$url );
        return $this->fetch();
    }


    public function delete(){
        $id = $this->request->param('id');
        $result = $this->validate(['id'=>$id],'app\common\validate\article.delete');
        if(true !== $result){
            return  json(['code'=>1, 'msg'=>$result]);
        }
        $article = articleModel::find($id);
        if($article) {
            try{
                articleModel::destroy($id);
            }catch (\Exception $e) {
                return  json(['code'=>1, 'msg'=>'删除失败!']);
            }
            return  json(['code'=>0, 'msg'=>'']);
        }

    }


    /**
     * 批量删除
     */
    public function multiDelete()
    {
        $ids = $this->request->param('ids');
        if (empty($ids) || is_array($ids)) {
            return  json(['code'=>1, 'msg'=>'it is not matching with multiple delete!']);
        }
        $id_arr = explode(',',$ids);
        try{
            articleModel::destroy($id_arr);
        }catch (\Exception $e) {
            return  json(['code'=>1, 'msg'=>$e->getMessage()]);
        }
        return  json(['code'=>0, 'msg'=>'']);
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