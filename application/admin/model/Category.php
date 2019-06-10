<?php

namespace app\admin\model;

use think\Db;
use think\Model;

class Category extends Model
{

    protected $autoWriteTimestamp = false;

    protected static function init(){

        self::beforeInsert(function ($data) {
        });
        self::afterInsert(function ($data) {
        });
        self::afterUpdate(function ($data) {
        });
        self::beforeDelete(function ($data) {
        });
    }

    public function getTree()
    {
//        $data = $this->where('is_del', '=', 0)->select();
        $data = $this->select();
        return $this->getTreeRec($data);
    }

    public function getTreeRec($data, $parent_id = 0, $level = 0, $isClear = TRUE)
    {
        static $ret = array();
        if ($isClear) {
            $ret = array();
        }
        foreach ($data as $k => $v) {
            if ($v['parent_id'] == $parent_id) {
                $v['level'] = $level;
                $ret[] = $v;
                $this->getTreeRec($data, $v['id'], $level + 1, FALSE);
            }
        }
        return $ret;
    }

    public function getChildren($id)
    {
        $data = $this->select();
        return $this->getChildrenRec($data, $id);
    }
    private function getChildrenRec($data, $parent_id=0, $isClear=TRUE)
    {
        static $ret = array();
        if($isClear)
            $ret = array();
        foreach ($data as $k => $v) {
            if($v['parent_id'] == $parent_id) {
                $ret[] = $v['id'];
                $this->getChildrenRec($data, $v['id'], FALSE);
            }
        }
        return $ret;
    }


}
