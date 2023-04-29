<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc\interfaces;

/**
 * @author    Vadim Volkovskyi <project.k.vadim@gmail.com>
 * @copyright (c) Vadim Volkovskyi 2022
 */
interface IJsonRpcBatch
{
    public function addItem(IJsonRpcBase $item): IJsonRpcBatch;

    public function getJsonRequest(): string;
}
