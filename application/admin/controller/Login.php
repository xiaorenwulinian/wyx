<?php
namespace app\admin\controller;
use think\captcha\Captcha;
use think\Controller;
use think\Db;
use think\Exception;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\12\22 0022
 * Time: 14:55
 */
class Login extends Controller {
    public function __construct(){
        parent::__construct();
    }

    public function login()
    {
        if($this->request->isPost()) {
            $allParam = $this->request->param();
            $captcha = new Captcha();
            if(!$captcha->check($allParam['verify'])) {
                return json(['code'=>1,'msg'=>'验证码错误']);
            }
            $adminData = Db::name('Admin')->where('username','=',$allParam['username'])->find();
            if($adminData) {
                if($adminData['id'] == 1 || $adminData['is_use'] == 1) {
                    if($adminData['password'] == md5($allParam['password'])) {
                        session('admin_user_id', $adminData['id']);
                        session('admin_username', $adminData['username']);
                        return json(['code'=>0,'msg'=>'']);
                    }
                    return json(['code'=>1,'msg'=>'用户名或密码错误']);
                }
                return json(['code'=>1,'msg'=>'账号被禁用']);
            }
            return json(['code'=>1,'msg'=>'用户名或密码错误！']);
        }
        return $this->fetch();
    }

    public function verify()
    {
        $config =    [
            // 验证码字体大小
            'fontSize'    =>    30,
            // 验证码位数
            'length'      =>    4,
            // 关闭验证码杂点
            'useNoise'    =>    false,
            'useCurve'    =>    false,
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }

    /**
     * 退出登陆
     */
    public function layout()
    {
        session('admin_user_id',null);
        return $this->redirect(url('admin/index/index'));
    }
}