<?php

namespace App\Api;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * ApiException.
 *
 * @author Mayur Patel <mayurpatel3209@gmail.com>
 */
class ApiException extends HttpException
{

    public function __construct($message = "Api Exception", $code = 0, $statusCode = 500, \Exception $previous = null, array $headers = array())
    {

        $headers['Exception-Type'] = 'API Exception';
        $headers['Generated-By'] = 'Laravel API Handler';
        parent::__construct($statusCode, $message, $previous, $headers, $code);

    }

}
