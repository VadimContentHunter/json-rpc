<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc\interfaces;

/**
 * @author    Vadim Volkovskyi <project.k.vadim@gmail.com>
 * @copyright (c) Vadim Volkovskyi 2022
 */
interface IJsonRpcError
{
    public function getCode(): int;

    public function getMessage(): string;

    /**
     * @return array<string, mixed>|null
     */
    public function getData(): ?array;
}
