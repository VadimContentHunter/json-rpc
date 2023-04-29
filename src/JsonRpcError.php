<?php
declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc;

use vadimcontenthunter\JsonRpc\interfaces\IJsonRpcError;
use vadimcontenthunter\JsonRpc\exceptions\JsonRpcException;

/**
 * @author    Vadim Volkovskyi <project.k.vadim@gmail.com>
 * @copyright (c) Vadim Volkovskyi 2022
 */
class JsonRpcError implements \JsonSerializable, IJsonRpcError
{
    public function __construct(
        protected int $code,
        protected string $message,
        /**
         * @var mixed[]
         */
        protected ?array $data = null,
    ) {
    }

    public function __serialize(): array
    {
        $result = [
            "code" => $this->code,
            "message" => $this->message,
        ];

        if ($this->data !== null) {
            $result += ["data" => $this->data];
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
        if (!is_array($data['data']) && $data['data'] !== null) {
            throw new JsonRpcException("Error, field 'Data', not an array or not null.");
        }

        $this->code = $data['code'] ?? throw new JsonRpcException("Error, no field 'code'.");
        $this->message = $data['message'] ?? throw new JsonRpcException("Error, no field 'message'.");
        $this->data = $data['data'] ?? null;
    }

    /**
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->__serialize();
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array<string,mixed>|null
     */
    public function getData(): ?array
    {
        return $this->data;
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
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data
        ];
    }
}
