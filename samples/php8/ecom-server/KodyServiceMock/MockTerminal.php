<?php

namespace Com\Kodypay\Grpc\Pay\V1;

class Terminal
{
    private $terminal_id;
    private $online;

    public function setTerminalId($terminal_id)
    {
        $this->terminal_id = $terminal_id;
    }

    public function setOnline($online)
    {
        $this->online = $online;
    }

    public function getTerminalId()
    {
        return $this->terminal_id;
    }

    public function isOnline()
    {
        return $this->online;
    }
}
