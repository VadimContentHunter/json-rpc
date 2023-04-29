<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc\interfaces;

use vadimcontenthunter\JsonRpc\interfaces\IJsonRpcBase;

/**
 * @author    Vadim Volkovskyi <project.k.vadim@gmail.com>
 * @copyright (c) Vadim Volkovskyi 2022
 */
interface IJsonRpcRequest extends IJsonRpcBase
{
    /**
     * @param array<string,mixed> $data
     */
    public static function createFromArray(array $data): IJsonRpcRequest;

    public static function createFromJson(string $json): IJsonRpcRequest;
}
