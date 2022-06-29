<?php
declare (strict_types = 1);
namespace cayu\tpuserlogin;

use cayu\tpuserlogin\concern\LoginService;

/**
 * Login.php
 * @author qjy 2022/6/16
 * @update qjy 2022/6/16
 * @mixin LoginService
 */
class Login
{
    /**
     * @param $method
     * @param $args
     * @return LoginService
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function __call($method, $args)
    {
        return call_user_func_array([LoginService::instance(), $method], $args);
    }
    
    public static function __callStatic($method, $args)
    {
        return call_user_func_array([LoginService::instance(), $method], $args);
    }
    
}