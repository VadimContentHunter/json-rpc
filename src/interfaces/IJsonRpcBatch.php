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

    public function getJson(): string;

    /**
     * @return IJsonRpcBase[]
     */
    public function getBatch(): array;

    public function createBatchRequestFromJson(string $json): IJsonRpcBatch;
}
