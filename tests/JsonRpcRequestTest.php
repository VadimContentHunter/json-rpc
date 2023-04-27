<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc\tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use vadimcontenthunter\JsonRpc\exceptions\JsonRpcException;
use vadimcontenthunter\JsonRpc\tests\fakes\JsonRpcRequestFake;

/**
 * @author    Vadim Volkovskyi <project.k.vadim@gmail.com>
 * @copyright (c) Vadim Volkovskyi 2022
 */
class JsonRpcRequestTest extends TestCase
{
    public function test__construct()
    {
        $request = new JsonRpcRequestFake('testMethod', ['param1' => 'value1', 'param2' => 'value2'], 12345);

        $this->assertEquals('testMethod', $request->fakeGetMethod());
        $this->assertEquals(['param1' => 'value1', 'param2' => 'value2'], $request->fakeGetParams());
        $this->assertEquals(12345, $request->fakeGetId());
    }

    public function test__serialize()
    {
        $request = new JsonRpcRequestFake('testMethod', ['param1' => 'value1', 'param2' => 'value2'], 12345);

        $this->assertEquals([
            "jsonrpc" => "2.0",
            "method" => "testMethod",
            "params" => ['param1' => 'value1', 'param2' => 'value2'],
            "id" => 12345,
        ], $request->__serialize());
    }

    public function test__unserialize_array()
    {
        $data = [
            'method' => 'testMethod',
            'params' => [
                'param1' => 'value1',
                'param2' => 'value2'
            ],
            'id' => 12345
        ];

        $request = new JsonRpcRequestFake('emptyMethod');
        $request->__unserialize($data);

        $this->assertEquals('testMethod', $request->fakeGetMethod());
        $this->assertEquals([
            'param1' => 'value1',
            'param2' => 'value2'
        ], $request->fakeGetParams());
        $this->assertEquals(12345, $request->fakeGetId());
    }

    public function test__unserialize_string()
    {
        $data = '{"method":"testMethod","params":{"param1":"value1","param2":"value2"},"id":12345}';

        $request = new JsonRpcRequestFake('emptyMethod');
        $request->__unserialize($data);

        $this->assertEquals('testMethod', $request->fakeGetMethod());
        $this->assertEquals([
            'param1' => 'value1',
            'param2' => 'value2'
        ], $request->fakeGetParams());
        $this->assertEquals(12345, $request->fakeGetId());
    }

    #[DataProvider('incorrectData')]
    public function test__unserialize_incorrectData($data)
    {
        $this->expectException(JsonRpcException::class);

        $request = new JsonRpcRequestFake('emptyMethod');
        $request->__unserialize($data);
    }

    public function incorrectData()
    {
        return [
            [null],
            [12345],
            ['incorrectString'],
            [[]],
            [['abc' => 'def']],
            [['def' => 'abc']],
        ];
    }

    public function testJsonSerialize()
    {
        $request = new JsonRpcRequestFake('testMethod', ['param1' => 'value1', 'param2' => 'value2'], 12345);

        $this->assertEquals([
            "jsonrpc" => "2.0",
            "method" => "testMethod",
            "params" => ['param1' => 'value1', 'param2' => 'value2'],
            "id" => 12345,
        ], $request->jsonSerialize());
    }
}
