<?php
declare (strict_types = 1);
namespace cayu\tpuserlogin\model;
/**
 * User.php
 * @author qjy 2022/6/16
 * @update qjy 2022/6/16
 */
class User extends \think\Model
{
    protected $table = '';
    
    public static $userInfo = null;
    
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
        if($data->isEmpty()){
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
        $realPassword = md5(md5($password.$userInfo['salt']));
        if($realPassword === $userInfo['password']){
            return true;
        }
    }
    
}