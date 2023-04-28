<?php

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc\tests;

use PHPUnit\Framework\TestCase;
use vadimcontenthunter\JsonRpc\JsonRpcError;
use PHPUnit\Framework\Attributes\DataProvider;
use vadimcontenthunter\JsonRpc\JsonRpcResponse;
use vadimcontenthunter\JsonRpc\exceptions\JsonRpcException;

/**
 * @author    Vadim Volkovskyi <project.k.vadim@gmail.com>
 * @copyright (c) Vadim Volkovskyi 2022
 */
class JsonRpcResponseTest extends TestCase
{
    // Тест на создание ответа только с результатом и идентификатором.
    public function testCreateResponseWithOnlyResultAndId(): void
    {
        $response = new JsonRpcResponse('test_result', 1, null);
        $this->assertInstanceOf(JsonRpcResponse::class, $response);
        $this->assertEquals('test_result', $response->getResult());
        $this->assertEquals(1, $response->getId());
        $this->assertNull($response->getError());
    }

    // Тест на создание ответа с ошибкой и идентификатором.
    public function testCreateResponseWithErrorAndId(): void
    {
        $error = new JsonRpcError(1, 'test error');
        $response = new JsonRpcResponse(null, 1, $error);
        $this->assertInstanceOf(JsonRpcResponse::class, $response);
        $this->assertNull($response->getResult());
        $this->assertEquals(1, $response->getId());
        $this->assertInstanceOf(JsonRpcError::class, $response->getError());
    }

    // Тест на создание ответа с результатом и ошибкой.
    public function testCreateResponseWithResultErrorAndIdThrowsError(): void
    {
        $this->expectExceptionMessage('Error, The Result field should be null when returning an error.');
        $error = new JsonRpcError(1, 'test error');
        new JsonRpcResponse('test_result', 1, $error);
    }

    // Тест на создание ответа без результатов, идентификатора или ошибок.
    public function testCreateResponseWithNoResultIdOrErrorThrowsError(): void
    {
        $this->expectExceptionMessage('Error, incorrect answer, an error should be indicated.');
        new JsonRpcResponse(null, null, null);
    }

    // Тест на создание ответа только с результатом.
    public function testCreateResponseWithResultButNoIdThrowsError(): void
    {
        $this->expectExceptionMessage('Error, If the response is without an error, then the id must be specified in the request.');
        new JsonRpcResponse('test_result', null, null);
    }

    // Тест на сериализацию ответа в JSON.
    public function testSerializeResponse(): void
    {
        $response = new JsonRpcResponse('test_result', 1, null);
        $jsonResponse = json_encode($response->__serialize());
        $this->assertEquals(json_encode([
            "jsonrpc" => "2.0",
            "id" => 1,
            "result" => "test_result"
        ]), $jsonResponse);
    }

    // Тест на десериализацию ответа из массива.
    public function testUnserializeArrayToResponse(): void
    {
        $data = json_decode('{"jsonrpc":"2.0", "id": 1, "result":"test_result"}', true);
        $response = new JsonRpcResponse("result", 1);
        $response->__unserialize($data);
        $this->assertInstanceOf(JsonRpcResponse::class, $response);
        $this->assertEquals('test_result', $response->getResult());
        $this->assertEquals(1, $response->getId());
        $this->assertNull($response->getError());
    }

    // Тест на десериализацию ответа из строки.
    public function testUnserializeStringToResponse(): void
    {
        $response = JsonRpcResponse::createFromJson('{"jsonrpc":"2.0", "id": 1, "result":"test_result"}');
        $this->assertInstanceOf(JsonRpcResponse::class, $response);
        $this->assertEquals('test_result', $response->getResult());
        $this->assertEquals(1, $response->getId());
        $this->assertNull($response->getError());
    }

    // Тест на проверку версии протокола;
    public function testGetVersion(): void
    {
        $response = new JsonRpcResponse('test_result', 1, null);
        $this->assertEquals('2.0', $response->getVersion());
    }

    // Тест на создание ответа с неправильным JSON.
    public function testCreateResponseWithInvalidJsonThrowsError(): void
    {
        $this->expectException(JsonRpcException::class);
        JsonRpcResponse::createFromJson('{"jsonrpc":"2.0", "id": 1 "result":"test_result"}');
    }
}
