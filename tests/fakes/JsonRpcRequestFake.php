<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc\tests\fakes;

use vadimcontenthunter\JsonRpc\JsonRpcRequest;
use vadimcontenthunter\JsonRpc\exceptions\JsonRpcException;

class JsonRpcRequestFake extends JsonRpcRequest
{
    public function fakeGetVersion(): string
    {
        return $this->version;
    }

    public function fakeGetMethod(): string
    {
        return $this->method;
    }

    /**
     * @return mixed[]
     */
    public function fakeGetParams(): array
    {
        return $this->params;
    }

    public function fakeGetId(): ?int
    {
        return $this->id;
    }
}
