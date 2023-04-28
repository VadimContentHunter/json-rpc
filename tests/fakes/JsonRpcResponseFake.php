<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc\tests\fakes;

use vadimcontenthunter\JsonRpc\JsonRpcResponse;
use vadimcontenthunter\JsonRpc\exceptions\JsonRpcException;

class JsonRpcResponseFake extends JsonRpcResponse
{
    public function fakeGetVersion(): string
    {
        return $this->version;
    }

    public function fakeGetMethod(): string
    {
        return $this->result;
    }

    public function fakeGetId(): ?int
    {
        return $this->id;
    }
}
