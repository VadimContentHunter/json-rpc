<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc\interfaces;

/**
 * @author    Vadim Volkovskyi <project.k.vadim@gmail.com>
 * @copyright (c) Vadim Volkovskyi 2022
 */
interface IJsonRpcResponse
{
    /**
     * @param array<string,mixed> $data
     */
    public static function createFromArray(array $data): IJsonRpcResponse;

    public static function createFromJson(string $json): IJsonRpcResponse;
}
