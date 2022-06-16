<?php
declare (strict_types = 1);
namespace cayu\tpuserlogin;

use cayu\tpuserlogin\concern\LoginService;

/**
 * LoginService.php
 * @author qjy 2022/6/16
 * @update qjy 2022/6/16
 */
class Login
{
    public function __call($method, $args)
    {
        return call_user_func_array([LoginService::instance(), $method], $args);
    }
    
    public static function __callStatic($method, $args)
    {
        return call_user_func_array([LoginService::instance(), $method], $args);
    }
    
}