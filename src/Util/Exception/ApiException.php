<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/10/20
 * Time: 下午3:51
 */
namespace Linyuee\Wechat\Util\Exception;

class ApiException extends \Exception
{
    protected $message;
    private $error_id;
    protected $code;

    function __construct($message = '', $errorId = 'HTTP_ERROR', $statusCode = 400)
    {
        parent::__construct($message,$statusCode);
        $this->code = $statusCode;
        $this->message = $message;
        $this->error_id = $errorId;
    }

    public function getHTTPStatus()
    {
        return $this->code;
    }


    public function getErrorId()
    {
        return $this->error_id;
    }

    public function getErrorMessage()
    {
        return empty($this->message)?'':$this->message;
    }

}