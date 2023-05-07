<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc\tests;

use PHPUnit\Framework\TestCase;
use vadimcontenthunter\JsonRpc\JsonRpcBatch;
use vadimcontenthunter\JsonRpc\JsonRpcError;
use vadimcontenthunter\JsonRpc\JsonRpcRequest;
use vadimcontenthunter\JsonRpc\JsonRpcResponse;

class JsonRpcBatchTest extends TestCase
{
    public function testAddItemRequest(): void
    {
        $batch = new JsonRpcBatch();
        $request1 = new JsonRpcRequest('foo', ['param1' => 'value1'], 1);
        $request2 = new JsonRpcRequest('bar', ['param2' => 'value2']);
        $batch->addItem($request1);
        $batch->addItem($request2);

        $json = $batch->getJson();
        $expectedJson = '[{"jsonrpc":"2.0","method":"foo","params":{"param1":"value1"},"id":1},{"jsonrpc":"2.0","method":"bar","params":{"param2":"value2"}}]';
        $this->assertEquals($expectedJson, $json);
    }

    public function testAddItemResponse(): void
    {
        $batch = new JsonRpcBatch();
        $request1 = new JsonRpcResponse(7, 1);
        $request2 = new JsonRpcResponse(null, null, new JsonRpcError(-32600, 'Invalid Request'));
        $request3 = new JsonRpcResponse(null, 5, new JsonRpcError(-32601, 'Method not found'));
        $batch->addItem($request1);
        $batch->addItem($request2);
        $batch->addItem($request3);

        $json = $batch->getJson();
        $expectedJson = '['
                        . '{"jsonrpc":"2.0","id":1,"result":7},'
                        . '{"jsonrpc":"2.0","id":null,"error":{"code":-32600,"message":"Invalid Request"}},'
                        . '{"jsonrpc":"2.0","id":5,"error":{"code":-32601,"message":"Method not found"}}'
                        . ']';
        $this->assertEquals($expectedJson, $json);
    }

    public function testGetBatch(): void
    {
        $request1 = new JsonRpcResponse(7, 1);
        $request2 = new JsonRpcResponse(null, null, new JsonRpcError(-32600, 'Invalid Request'));
        $request3 = new JsonRpcResponse(null, 5, new JsonRpcError(-32601, 'Method not found'));

        $expected = [
            $request1,
            $request2,
            $request3,
        ];

        $batch = new JsonRpcBatch();
        $batch->addItem($request1);
        $batch->addItem($request2);
        $batch->addItem($request3);
        $this->assertEquals($expected, $batch->getBatch());
    }

    public function testCreateBatchRequestFromJson(): void
    {
        $expected = [
            new JsonRpcRequest('testMethod', ['param1' => 'value1', 'param2' => 'value2'], 1),
            new JsonRpcRequest('foo', ['param1' => 'value1'], 2),
            new JsonRpcRequest('bar', ['param2' => 'value2']),
        ];

        $json = '['
                . '{"jsonrpc":"2.0","method":"testMethod","params":{"param1":"value1","param2":"value2"},"id":1}'
                . ',{"jsonrpc":"2.0","method":"foo","params":{"param1":"value1"},"id":2}'
                . ',{"jsonrpc":"2.0","method":"bar","params":{"param2":"value2"}}'
                . ']';
        $batch = new JsonRpcBatch();
        $result = $batch->createBatchRequestFromJson($json)->getBatch();
        $this->assertEquals($expected, $result);
    }
}
