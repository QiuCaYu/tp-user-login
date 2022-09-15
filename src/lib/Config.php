<?php
declare (strict_types = 1);
namespace cayu\tpuserlogin\lib;

use cayu\tpuserlogin\concern\LoginService;
use cayu\tpuserlogin\exception\ValidateErrorException;
use cayu\tpuserlogin\model\User;

/**
 * Config.php
 * @author qjy 2022/6/17
 * @update qjy 2022/6/17
 */
abstract class Config
{
    protected static $_init = null;
    
    protected $modelClass = null;
    
    public $user = null;
    
    public $app;
    
    public $appId = '';
    
    public $filter = [];
    
    public $token;
    
    public $tokenInfo;
    
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
     * @author qjy 2022/6/17
     * @update qjy 2022/6/17
     */
    public function getCacheConfig(){
        $cacheData = $this->app['cache']??null;
        if(!isset($cacheData)){
            throw new ValidateErrorException($this->getResponseConfig('411'));
        }
        return $cacheData;
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
        if(empty($this->app['table']) || empty($this->app['connection'])){
            throw new ValidateErrorException($this->getResponseConfig('410'));
        }
        if($this->modelClass === null){
            $this->modelClass = new User();
        }
        return $this->modelClass;
    }
    
    /**
     * @return mixed
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function user($token = null)
    {
        if($token !== null){
            $cacheData = $this->getCacheConfig();
            // 如果key不为空，则获取值
            $this->token = $token;
            $tokenInfo = cache($cacheData['token_prefix'].$token);
            if($tokenInfo){
                $this->user = cache($tokenInfo['key']);
            }
        }
        if($this->user === null){
            throw new ValidateErrorException($this->getResponseConfig('430'));
        }
        // 过滤字段
        foreach ($this->filter as $value) {
            if(isset($this->user[$value])){
                unset($this->user[$value]);
            }
        }
        return $this->user;
    }
    
    /**
     * @param $token
     * @return mixed|object|\think\App
     * @author qjy 2022/6/23
     */
    public function token($token = null,$filter = ['source_app_id']){
        if($token !== null){
            $cacheData = $this->getCacheConfig();
            $this->tokenInfo = cache($cacheData['token_prefix'].$token);
        }
        if($filter){
            foreach ($filter as $value) {
                if(isset($this->tokenInfo[$value])){
                    unset($this->tokenInfo[$value]);
                }
            }
        }
        return $this->tokenInfo;
    }
}