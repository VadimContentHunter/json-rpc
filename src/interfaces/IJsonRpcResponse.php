<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc\interfaces;

use vadimcontenthunter\JsonRpc\interfaces\IJsonRpcError;

/**
 * @author    Vadim Volkovskyi <project.k.vadim@gmail.com>
 * @copyright (c) Vadim Volkovskyi 2022
 */
interface IJsonRpcResponse extends IJsonRpcBase
{
    /**
     * @param array<string,mixed> $data
     */
    public static function createFromArray(array $data): IJsonRpcResponse;

    public static function createFromJson(string $json): IJsonRpcResponse;

    public function getVersion(): string;

    public function getResult(): mixed;

    public function getId(): ?int;

    public function getError(): ?IJsonRpcError;
}
