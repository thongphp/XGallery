<?php

namespace App\Exceptions;

class OAuthClientException extends \Exception
{
    public function __construct($errorMessage = "", $code = 0)
    {
        $message = sprintf('[%s] <%d> %s', __CLASS__, $code, $errorMessage);

        parent::__construct($message);
    }
}
