<?php
declare (strict_types = 1);
namespace cayu\tpuserlogin\concern;

use cayu\tpuserlogin\exception\ValidateLoginException;
use cayu\tpuserlogin\model\User;

/**
 * LoginService.php
 * @author qjy 2022/6/16
 * @update qjy 2022/6/16
 */
class LoginService
{
    protected static $_init = null;
    
    protected static $model = null;
    
    public $user = null;
    
    public $app;
    
    public $filter = [
        'password',
        'salt',
    ];
    
    public static $userList = [];
    
    /**
     * @return static
     * @author qjy 2022/5/28
     * @update qjy 2022/5/28
     */
    public static function instance(){
        if(!self::$_init){
            self::$_init = new static();
        }
        return self::$_init;
    }
    
    /**
     * Get the tplogin configuration.
     *
     * @param string $name
     * @return array
     */
    protected function getConfig($name)
    {
        return config("tplogin.meta.{$name}",'tplogin.meta.default');
    }
    
    /**
     * @param $name
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function app(string $name = 'default'){
        $this->app = $this->getConfig($name);
    }
    
    /**
     * @return User
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function model():User
    {
        if (self::$model === null) {
            self::$model = new User();
        }
        self::$model->setTable($this->app['table']);
        return self::$model;
    }
    
    /**
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function validator($username, $password)
    {
        $userInfo = $this->model()->checkAccount($username);
        if ($userInfo === false) {
            throw new ValidateLoginException('获取用户信息失败');
        }
        $this->user = $userInfo;
        $userPasswordStatus = $this->model()->checkAccountPassword([$username, $password]);
        if ($userPasswordStatus === false) {
            throw new ValidateLoginException('校验账号密码有误');
        }
        return true;
    }
    
    /**
     * @return mixed
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function getUser()
    {
        if($this->user === null){
            throw new ValidateLoginException('获取用户信息失败，请检查验证流程');
        }
        return $this->user->except($this->filter);
    }
}