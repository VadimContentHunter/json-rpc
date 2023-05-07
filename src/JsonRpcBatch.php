<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc;

use vadimcontenthunter\JsonRpc\JsonRpcRequest;
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

    public function getJson(): string
    {
        $json = '[';
        foreach ($this->jsonRpsItems as $key => $item) {
            $json .= $item->getJsonRequest() . ',';
        }
        $json = strlen($json) > 1 ? substr($json, 0, -1) : '';
        $json .= ']';

        return $json;
    }

    /**
     * @return IJsonRpcBase[]
     */
    public function getBatch(): array
    {
        return $this->jsonRpsItems;
    }

    public function createBatchRequestFromJson(string $json): IJsonRpcBatch
    {
        if (preg_match('~^\[(?<body>{.+[},])*]$~u', $json, $matches)) {
            $items_json = preg_split("~}\s*,\s*{~u", $matches['body']);
            foreach ($items_json as $item) {
                $item = str_contains($item[0], '{') ? $item : '{' . $item;
                $item = str_contains($item[-1], '}') ? $item : $item . '}';
                $this->jsonRpsItems[] = JsonRpcRequest::createFromJson($item);
            }
        } else {
            try {
                $this->jsonRpsItems[] = JsonRpcRequest::createFromJson($json);
            } catch (JsonRpcException $jre) {
                throw $jre;
            } catch (\Exception $e) {
                throw new JsonRpcException("Error, incorrect json for batch.");
            }
        }

        return $this;
    }
}
