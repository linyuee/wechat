<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/10/20
 * Time: 下午3:51
 */
namespace Linyuee\Wechat\Util\Exception;

class ClientException extends \Exception
{
    protected $message;
    protected $code;

    function __construct($message = '', $statusCode = 400)
    {
        parent::__construct($message,$statusCode);
        $this->code = $statusCode;
        $this->message = $message;
    }


}