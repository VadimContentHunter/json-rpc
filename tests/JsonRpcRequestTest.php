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
    public function test_construct(): void
    {
        $request = new JsonRpcRequestFake('testMethod', ['param1' => 'value1', 'param2' => 'value2'], 12345);

        $this->assertEquals('testMethod', $request->fakeGetMethod());
        $this->assertEquals(['param1' => 'value1', 'param2' => 'value2'], $request->fakeGetParams());
        $this->assertEquals(12345, $request->fakeGetId());
    }

    public function test_serialize(): void
    {
        $request = new JsonRpcRequestFake('testMethod', ['param1' => 'value1', 'param2' => 'value2'], 12345);

        $this->assertEquals([
            "jsonrpc" => "2.0",
            "method" => "testMethod",
            "params" => ['param1' => 'value1', 'param2' => 'value2'],
            "id" => 12345,
        ], $request->__serialize());
    }

    public function test_serialize_withoutParamsAndId(): void
    {
        $request = new JsonRpcRequestFake('testMethod');

        $this->assertEquals([
            "jsonrpc" => "2.0",
            "method" => "testMethod",
        ], $request->__serialize());
    }

    public function test_unserialize_array(): void
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

    public function test_unserialize_string(): void
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

    public function test_unserialize_withoutParamsAndId(): void
    {
        $data = '{"method":"testMethod"}';

        $request = new JsonRpcRequestFake('emptyMethod');
        $request->__unserialize($data);

        $this->assertEquals('testMethod', $request->fakeGetMethod());
        $this->assertEquals([], $request->fakeGetParams());
        $this->assertEquals(null, $request->fakeGetId());
    }

    /**
     * @param mixed[] $data
     */
    #[DataProvider('incorrectData')]
    public function test_unserialize_incorrectData(array $data): void
    {
        $this->expectException(JsonRpcException::class);

        $request = new JsonRpcRequestFake('emptyMethod');
        $request->__unserialize($data);
    }

    /**
     * @return mixed[]
     */
    public static function incorrectData(): array
    {
        return [
            ['test_1' => [123]],
            ['test_2' => ['abc' => 'def']],
            ['test_3' => ['def' => 'abc']],
        ];
    }

    public function test_JsonSerialize(): void
    {
        $request = new JsonRpcRequestFake('testMethod', ['param1' => 'value1', 'param2' => 'value2'], 12345);

        $this->assertEquals([
            "jsonrpc" => "2.0",
            "method" => "testMethod",
            "params" => ['param1' => 'value1', 'param2' => 'value2'],
            "id" => 12345,
        ], $request->jsonSerialize());
    }

    public function test_JsonSerialize_withoutParamsAndId(): void
    {
        $request = new JsonRpcRequestFake('testMethod');

        $this->assertEquals([
            "jsonrpc" => "2.0",
            "method" => "testMethod",
        ], $request->jsonSerialize());
    }

    public function test_JsonRpcRequest_on_json_encode(): void
    {
        $expected = '{"jsonrpc":"2.0","method":"testMethod","params":{"param1":"value1","param2":"value2"},"id":12345}';
        $request = new JsonRpcRequestFake('testMethod', ['param1' => 'value1', 'param2' => 'value2'], 12345);

        $this->assertEquals($expected, json_encode($request));
    }

    public function test_JsonRpcRequest_on_json_encode_withoutParamsAndId(): void
    {
        $expected = '{"jsonrpc":"2.0","method":"testMethod"}';
        $request = new JsonRpcRequestFake('testMethod');

        $this->assertEquals($expected, json_encode($request));
    }

    /**
     * Проверяет метод composeArray класса JsonRpcError. Он убеждается в том, что метод возвращает
     * ожидаемый набор данных в формате JSON-RPC.
     */
    public function testRpcErrorComposeArray(): void
    {
        $request = new JsonRpcRequestFake('testMethod', ['param1' => 'value1', 'param2' => 'value2'], 12345);
        $expectedResult = [
            "version" => "2.0",
            "method" => "testMethod",
            "params" => ['param1' => 'value1', 'param2' => 'value2'],
            "id" => 12345,
        ];

        $this->assertSame($expectedResult, $request->composeArray());
    }

    public function testGetJsonRequest(): void
    {
        $response_json = '{"jsonrpc":"2.0","method":"subtract","params":[42,23],"id":1}';
        $response = JsonRpcRequestFake::createFromJson($response_json);
        $this->assertEquals($response_json, $response->getJsonRequest());
    }
}
