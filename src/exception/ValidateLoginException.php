<?php
declare (strict_types = 1);

namespace cayu\tpuserlogin\exception;
/**
 * 数据验证异常
 */
class ValidateLoginException extends \RuntimeException
{
    protected $error;
    
    public function __construct($error)
    {
        $this->error   = $error;
        $this->message = is_array($error) ? $error['system_error_message'] : $error;
    }
    
    /**
     * 获取验证错误信息
     * @access public
     * @return array|string
     */
    public function getError()
    {
        return $this->error;
    }
}