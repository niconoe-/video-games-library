<?php


class VGL_Utils_CreateMessage
{
    public static function info($message)
    {
        return self::_message($message, __FUNCTION__);
    }

    public static function success($message)
    {
        return self::_message($message, __FUNCTION__);
    }

    public static function warning($message)
    {
        return self::_message($message, __FUNCTION__);
    }

    public static function danger($message)
    {
        return self::_message($message, __FUNCTION__);
    }


    protected static function _message($msg, $type)
    {
        return (object)['message' => $msg, 'type' => $type];
    }

    public static function getMessagesByType($aObjectMessages)
    {
        $aArrayMessages = [];
        foreach ($aObjectMessages as $oMessage) {
            if (isset($aArrayMessages[$oMessage->type])) {
                $aArrayMessages[$oMessage->type][] = $oMessage->message;
            } else {
                $aArrayMessages[$oMessage->type] = [$oMessage->message];
            }
        }

        return $aArrayMessages;

    }
}