<?php

namespace InstagramAPI;

class LogoutResponse extends Response
{
    public function __construct($response)
    {
        if (self::STATUS_OK == $response['status']) {
            $this->setFullResponse($response);
        } else {
            $this->setMessage($response['message']);
        }
        $this->setStatus($response['status']);
    }
}
