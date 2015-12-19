<?php

namespace Wee;

class Exception extends \Exception
{
    public function __construct($message, $code = 500)
    {
        parent::__construct($message, (int) $code);
    }
}

// class WarningException extends ErrorException
// {}

// class ParseException extends ErrorException
// {}

// class NoticeException extends ErrorException
// {}

// class CoreErrorException extends ErrorException
// {}

// class CoreWarningException extends ErrorException
// {}

// class CompileErrorException extends ErrorException
// {}

// class CompileWarningException extends ErrorException
// {}

// class UserErrorException extends ErrorException
// {}

// class UserWarningException extends ErrorException
// {}

// class UserNoticeException extends ErrorException
// {}

// class StrictException extends ErrorException
// {}

// class RecoverableErrorException extends ErrorException
// {}

// class DeprecatedException extends ErrorException
// {}

// class UserDeprecatedException extends ErrorException
// {}
