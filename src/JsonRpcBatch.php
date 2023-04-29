<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc;

use vadimcontenthunter\JsonRpc\interfaces\IJsonRpcBase;
use vadimcontenthunter\JsonRpc\interfaces\IJsonRpcBatch;
use vadimcontenthunter\JsonRpc\exceptions\JsonRpcException;

/**
 * @author    Vadim Volkovskyi <project.k.vadim@gmail.com>
 * @copyright (c) Vadim Volkovskyi 2022
 */
class JsonRpcBatch implements IJsonRpcBatch
{
    /**
     * @var IJsonRpcBase[]
     */
    protected array $jsonRpsItems;

    public function addItem(IJsonRpcBase $item): IJsonRpcBatch
    {
        $this->jsonRpsItems[] = $item;
        return $this;
    }

    public function getJsonRequest(): string
    {
        $json = '[';
        foreach ($this->jsonRpsItems as $key => $item) {
            $json .= $item->getJsonRequest() . ',';
        }
        substr($json, 0, -1);
        $json .= ']';

        return $json;
    }
}
