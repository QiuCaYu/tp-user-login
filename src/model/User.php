<?php
declare (strict_types = 1);
namespace cayu\tpuserlogin\model;
use cayu\tpuserlogin\Login;

/**
 * User.php
 * @author qjy 2022/6/16
 * @update qjy 2022/6/16
 */
class User extends \think\Model
{
    
    public static $userInfo = null;
    
    
    public function __construct(array $data = [])
    {
        $this->setConnection(Login::instance()->app['connection']);
        $this->setTable(Login::instance()->app['table']);
        parent::__construct($data);
    }
    
    /**
     * @param $table
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function setTable($table){
        $this->table = $table;
    }
    
    /**
     * @param $username
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function checkAccount(string $username){
        $data = $this->where('account',$username)->find();
        if(!$data){
            return false;
        }
        // 查询后赋值给静态变量
        self::$userInfo[$username] = $data;
        return self::$userInfo[$username];
    }
    
    /**
     * @param $data
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function checkAccountPassword($data){
        list($username,$password) = $data;
        if(empty(self::$userInfo[$username])){
            return false;
        }
        $userInfo = self::$userInfo[$username];
        $secret = $password.$userInfo->salt;
        $realPassword = md5(md5($secret));
        unset($secret);
        if($realPassword !== $userInfo->password){
            return false;
        }
        return true;
    }
    
}