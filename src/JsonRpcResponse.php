<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc;

use vadimcontenthunter\JsonRpc\exceptions\JsonRpcException;
use vadimcontenthunter\JsonRpc\interfaces\IJsonRpcResponse;

/**
 * @author    Vadim Volkovskyi <project.k.vadim@gmail.com>
 * @copyright (c) Vadim Volkovskyi 2022
 */
class JsonRpcResponse implements \JsonSerializable, IJsonRpcResponse
{
    protected string $version = '2.0';

    public function __construct(
        protected mixed $result = null,
        protected ?int $id = null,
        protected ?JsonRpcError $error = null,
    ) {
        if ($result === null && $id === null && $error === null) {
            throw new JsonRpcException("Error, incorrect answer, an error should be indicated.");
        }

        if ($result !== null && $id === null && $error === null) {
            throw new JsonRpcException("Error, If the response is without an error, then the id must be specified in the request.");
        }

        if ($result !== null && $error !== null) {
            throw new JsonRpcException("Error, The Result field should be null when returning an error.");
        }
    }

    /**
     * @return array<string,mixed>
     */
    public function __serialize(): array
    {
        $result = [
            "jsonrpc" => $this->version,
            "id" => $this->id,
        ];

        if ($this->id !== null && $this->error === null) {
            $result += ["result" => $this->result];
        }

        if ($this->result === null && $this->error !== null) {
            $result += ["error" => $this->error];
        }

        return $result;
    }

    public function __unserialize(mixed $data): void
    {
        if (is_array($data)) {
        } elseif (is_string($data)) {
            $json = json_decode($data, true);
            if ($json === false) {
                throw new JsonRpcException("Error, failed to parse json line.");
            } elseif ($json === null) {
                throw new JsonRpcException("Error, json cannot be converted or the encoded data contains more nested levels than the specified nesting limit.");
            }
            $data = $json;
        } else {
            throw new JsonRpcException("Error, incorrect data.");
        }

        if ($data['result'] === null && $data['id'] === null && $data['error'] === null) {
            throw new JsonRpcException("Error, incorrect answer, an error should be indicated.");
        }

        if ($data['result'] !== null && $data['id'] === null && $data['error'] === null) {
            throw new JsonRpcException("Error, If the response is without an error, then the id must be specified in the request.");
        }

        if ($data['result'] !== null && $data['error'] !== null) {
            throw new JsonRpcException("Error, The Result field should be null when returning an error.");
        }

        $this->result = $data['result'] ?? null;
        $this->id = $data['id'] ?? null;
        $this->error = $data['error'] ?? null;
    }

    /**
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->__serialize();
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getError(): ?JsonRpcError
    {
        return $this->error;
    }

    public function getJsonRequest(): string
    {
        $json = json_encode($this);
        return is_string($json) ? $json : throw new JsonRpcException("Error, Incorrect json.");
    }

    /**
     * @return array<string,mixed>
     */
    public function composeArray(): array
    {
        return [
            'version' => $this->version,
            'result' => $this->result,
            'id' => $this->id,
            'error' => $this->error
        ];
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function createFromArray(array $data): IJsonRpcResponse
    {
        $result = $data['result'] ?? null;
        $id = $data['id'] ?? null;
        $error = null;
        if ($data['error'] instanceof JsonRpcError) {
            $error = $data['error'];
        }

        return new self($result, $id, $error);
    }

    public static function createFromJson(string $json): IJsonRpcResponse
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
