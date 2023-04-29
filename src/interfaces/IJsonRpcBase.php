<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc\interfaces;

/**
 * @author    Vadim Volkovskyi <project.k.vadim@gmail.com>
 * @copyright (c) Vadim Volkovskyi 2022
 */
interface IJsonRpcBase
{
    /**
     * @return array<string, mixed>
     */
    public function __serialize(): array;

    public function __unserialize(mixed $data): void;

    public function getJsonRequest(): string;

    /**
     * @return array<string,mixed>
     */
    public function composeArray(): array;
}
