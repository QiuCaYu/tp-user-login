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
    
    public $filter = [];
    
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
        $this->filter = $this->app['filter_field'];
        return $this;
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
        // 没有调用，则取默认
        if(!$this->app){
            $this->app();
        }
        if(!isset($this->app['table']) || !isset($this->app['response_code'])){
            $tips = '请检查文件配置';
            $errorConfig = [
                'code' => '410',
                'system_error_message' => $tips,
                'message' => $tips,
            ];
            throw new ValidateLoginException($errorConfig);
        }
        self::$model->setTable($this->app['table']);
        return self::$model;
    }
    
    /**
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function validator(string $username,string $password)
    {
        $userInfo = $this->model()->checkAccount($username);
        if ($userInfo === false) {
            throw new ValidateLoginException($this->app['response_code']['420']);
        }
        $this->user = $userInfo;
        $userPasswordStatus = $this->model()->checkAccountPassword([$username, $password]);
        if ($userPasswordStatus === false) {
            throw new ValidateLoginException($this->app['response_code']['421']);
        }
        return true;
    }
    
    /**
     * @return mixed
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function user()
    {
        if($this->user === null){
            $errorConfig = [
                'code' => '430',
                'system_error_message' => '流程错误，需要先进行检验，再获取用户信息',
                'message' => '系统繁忙',
            ];
            throw new ValidateLoginException($errorConfig);
        }
        // 过滤字段
        foreach ($this->filter as $value) {
            if(isset($this->user[$value])){
                unset($this->user[$value]);
            }
        }
        return $this->user;
    }
}