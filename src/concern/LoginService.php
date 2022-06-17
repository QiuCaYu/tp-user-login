<?php
declare (strict_types = 1);
namespace cayu\tpuserlogin\concern;

use cayu\tpuserlogin\exception\ValidateErrorException;
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
    
    public $token;
    
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
     * Get the tplogin configuration.
     *
     * @param string $code
     * @return array
     */
    protected function getResponseConfig($code)
    {
        if(isset($this->app['response_code']) && isset($this->app['response_code'][$code])){
            return $this->app['response_code'][$code];
        }
        return config("tplogin.response_code.{$code}");
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
        if(!isset($this->app['table'])){
            $tips = '请检查文件配置';
            $errorConfig = [
                'code' => '410',
                'system_error_message' => $tips,
                'message' => $tips,
            ];
            throw new ValidateErrorException($errorConfig);
        }
        self::$model->setTable($this->app['table']);
        return self::$model;
    }
    
    /**
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function inspectUser(string $username,string $password)
    {
        if(empty($username) || empty($password)){
            throw new ValidateErrorException($this->getResponseConfig('400'));
        }
        $userInfo = $this->model()->checkAccount($username);
        if ($userInfo === false) {
            throw new ValidateErrorException($this->getResponseConfig('420'));
        }
        $this->user = $userInfo;
        $userPasswordStatus = $this->model()->checkAccountPassword([$username, $password]);
        if ($userPasswordStatus === false) {
            throw new ValidateErrorException($this->getResponseConfig('421'));
        }
        $this->cache();
        return $this;
    }
    
    /**
     * @return mixed|object|\think\App
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function cache(){
        $cacheData = $this->app['cache']??null;
        if(!isset($cacheData)){
            $tips = '请检查文件缓存配置';
            $errorConfig = [
                'code' => '411',
                'system_error_message' => $tips,
                'message' => $tips,
            ];
            throw new ValidateErrorException($errorConfig);
        }
        $this->token = md5($this->user->username.microtime());
        return cache($cacheData['prefix'].$this->token,$this->user(),$cacheData['times']);
    }
    
    /**
     * @return mixed
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function token(){
        return $this->token;
    }
    
    /**
     * @return mixed
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function user($token = null)
    {
        if($token !== null){
            $cacheData = $this->app['cache'];
            // 如果key不为空，则获取值
            $this->token = $token;
            $this->user = cache($cacheData['prefix'].$token);
        }
        if($this->user === null){
            $errorConfig = [
                'code' => '430',
                'system_error_message' => '用户信息不存在',
                'message' => '系统繁忙',
            ];
            throw new ValidateErrorException($errorConfig);
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