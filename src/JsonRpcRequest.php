<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc;

use vadimcontenthunter\JsonRpc\exceptions\JsonRpcException;

/**
 * @author    Vadim Volkovskyi <project.k.vadim@gmail.com>
 * @copyright (c) Vadim Volkovskyi 2022
 */
class JsonRpcRequest implements \JsonSerializable
{
    protected string $version = '2.0';

    public function __construct(
        protected string $method,
        /**
         * @var mixed[]
         */
        protected array $params = [],
        protected ?int $id = null,
    ) {
    }

    public function __serialize(): array
    {
        return [
            "jsonrpc" => $this->version,
            "method" => $this->method,
            "params" => $this->params,
            "id" => $this->id,
        ];
    }

    public function __unserialize(mixed $data): void
    {
        if (is_array($data)) {
            $this->method = $data['method'] ?? throw new JsonRpcException("Error, failed to parse json line.");
            $this->params = $data['params'] ?? [];
            $this->id = $data['id'] ?? null;
        } elseif (is_string($data)) {
            $json = json_decode($data, true);
            if ($json === false) {
                throw new JsonRpcException("Error, failed to parse json line.");
            } elseif ($json === null) {
                throw new JsonRpcException("Error, json cannot be converted or the encoded data contains more nested levels than the specified nesting limit.");
            }

            $this->method = $json['method'];
            $this->params = $json['params'] ?? [];
            $this->id = $json['id'] ?? null;
        } else {
            throw new JsonRpcException("Error, incorrect data.");
        }
    }

    public function jsonSerialize(): array
    {
        return $this->__serialize();
    }
}
