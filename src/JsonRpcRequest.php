<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc;

use vadimcontenthunter\JsonRpc\interfaces\IJsonRpcRequest;
use vadimcontenthunter\JsonRpc\exceptions\JsonRpcException;

/**
 * @author    Vadim Volkovskyi <project.k.vadim@gmail.com>
 * @copyright (c) Vadim Volkovskyi 2022
 */
class JsonRpcRequest implements \JsonSerializable, IJsonRpcRequest
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

    /**
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        $result = [
            "jsonrpc" => $this->version,
            "method" => $this->method,
        ];

        if (count($this->params) !== 0) {
            $result += ["params" => $this->params];
        }

        if ($this->id !== null) {
            $result += ["id" => $this->id];
        }

        return $result;
    }

    public function __unserialize(mixed $data): void
    {
        if (is_array($data)) {
            $this->method = $data['method'] ?? throw new JsonRpcException("Error, incorrect data.");
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

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->__serialize();
    }

    public function getJsonRequest(): string
    {
        $json = json_encode($this);
        return is_string($json) ? $json : throw new JsonRpcException("Error, Incorrect json.");
    }

    /**
     * @return array<string, mixed>
     */
    public function composeArray(): array
    {
        return [
            'version' => $this->version,
            'method' => $this->method,
            'params' => $this->params,
            'id' => $this->id
        ];
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return mixed[]
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function createFromArray(array $data): JsonRpcRequest
    {
        $method = $data['method'] ?? throw new JsonRpcException("Error, incorrect data.");
        $params = $data['params'] ?? [];
        $id = $data['id'] ?? null;

        return new self($method, $params, $id);
    }

    public static function createFromJson(string $json): JsonRpcRequest
    {
        $data = json_decode($json, true);
        if ($data === false) {
            throw new JsonRpcException("Error, failed to parse json line.");
        } elseif ($data === null) {
            throw new JsonRpcException("Error, json cannot be converted or the encoded data contains more nested levels than the specified nesting limit.");
        }
        return self::createFromArray($data);
    }
}
