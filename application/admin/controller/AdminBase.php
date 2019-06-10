<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;

class AdminBase extends Controller
{
    public function __construct(){
        parent::__construct();

        if(!session('admin_user_id')) {
            return $this->redirect(url('login/login'));
        }

    }

    public function setPageBtn($title, $btnName, $btnLink){
        $this->assign([
            '_page_title'=>$title,
            '_page_btn_name'=>$btnName,
            '_page_btn_link'=>$btnLink
        ]);
    }
}
