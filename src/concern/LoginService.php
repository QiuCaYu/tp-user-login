<?php
declare (strict_types = 1);
namespace cayu\tpuserlogin\concern;

use cayu\tpuserlogin\exception\ValidateErrorException;
use cayu\tpuserlogin\lib\Config;
use cayu\tpuserlogin\model\User;

/**
 * LoginService.php
 * @author qjy 2022/6/16
 * @update qjy 2022/6/16
 */
class LoginService extends Config
{
    /**
     * 账号检查者
     * @param string $username
     * @param string $password
     * @author qjy 2022/6/23
     */
    public function checker(string $username,string $password){
        if(empty($username) || empty($password)){
            throw new ValidateErrorException($this->getResponseConfig('400'));
        }
        $userInfo = $this->model()->checkAccount($username);

        if ($userInfo === false) {
            throw new ValidateErrorException($this->getResponseConfig('420'));
        }
        $userPasswordStatus = $this->model()->checkAccountPassword([$username, $password]);
        if ($userPasswordStatus === false) {
            throw new ValidateErrorException($this->getResponseConfig('421'));
        }
        return $userInfo;
    }
    
    /**
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function inspectUser(string $username,string $password,string $appId = '')
    {
        $this->appId = $appId;
        $this->user = $this->checker($username,$password);
        $this->user['source_app_id'] = $this->appId;
        $this->cache();
        return $this;
    }
    
    /**
     * 更新用户密码
     * @param string $username
     * @param string $password
     * @param string $newPassword
     * @return User
     * @author qjy 2022/6/29
     */
    public function editPassword(string $username,string $password,string $newPassword){
        $this->user = $this->checker($username,$password);
        // 生成新密码
        $salt = (string)rand(0000,9999);
        $newMdPasswrord = md5(md5($newPassword.$salt));
        // 密码更新
        $update = [
            'salt' => $salt,
            'password' => $newMdPasswrord,
            'update_time' => date('Y-m-d H:i:s')
        ];
        $where = ['id' => $this->user->id];
        return $this->model()::update($update,['id' => $where]);
    }
    
    /**
     * @return mixed|object|\think\App
     * @author qjy 2022/6/16
     * @update qjy 2022/6/16
     */
    public function cache(){
        $cacheData = $this->getCacheConfig();
        // 设置每个用户的存储token列表key
        $tokenListKey = 'user_token_list:'.$this->user->username;
        $tokenList = cache($tokenListKey);
        if($tokenList === null){
            $tokenList = [];
        }
        // 缓存用户信息
        $userKey = $cacheData['user_prefix'].$this->user->username;
        $userCacheStatus = cache($userKey,$this->user());
        if($userCacheStatus !== true){
            throw new ValidateErrorException($this->getResponseConfig('435'));
        }
        // 生成token
        $tokenExpiredTime = date('Y-m-d H:i:s',strtotime('+'.$cacheData['times'].'seconds'));
        $this->tokenInfo = [
            'key' =>$userKey,
            'token_expired_time' => $tokenExpiredTime,
        ];
        // token
        $this->token = md5($this->user->username.microtime());
        // 获取token数据
        $tokenStatus = cache($cacheData['token_prefix'].$this->token,$this->tokenInfo,$cacheData['times']);
        // 新增token到用户的tokenList
        $tokenList[] = $this->token;
        if(count($tokenList) > 10){
            foreach ($tokenList as $delKey => $delToken) {
                if($delKey <= 9){
                    cache($cacheData['token_prefix'].$delToken,null);
                    unset($tokenList[$delKey]);
                }
            }
            $tokenList = array_values($tokenList);
        }
        $tokenListStatus = cache($tokenListKey,$tokenList);
        return $tokenStatus && $tokenListStatus;
    }
    
    /**
     * 删除token以及用户信息
     * @param null $token
     * @author qjy 2022/6/17
     * @update qjy 2022/6/17
     */
    public function destroy($token = null){
        $this->user = null;
        if($token !== null){
            $this->token = $token;
        }
        $cacheData = $this->getCacheConfig();
        $check = cache($cacheData['token_prefix'].$this->token,null);
        if($check === true){
            return true;
        }
        return false;
    }
    
    public function tokenKey(){
        return $this->token;
    }

}