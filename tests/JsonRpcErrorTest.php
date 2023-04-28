<?php

/**
 * Набор тестов PHPUnit для класса JsonRpcError.
 *
 * @see JsonRpcError
 * @see JsonRpcException
 */

declare(strict_types=1);

namespace vadimcontenthunter\JsonRpc\tests;

use PHPUnit\Framework\TestCase;
use vadimcontenthunter\JsonRpc\JsonRpcError;
use vadimcontenthunter\JsonRpc\exceptions\JsonRpcException;

final class JsonRpcErrorTest extends TestCase
{
    /**
     * Проверяет, что объект JsonRpcError может быть правильно сериализован и десериализован,
     * и что значения его свойств до и после сериализации совпадают.
     */
    public function testSerialization(): void
    {
        $error = new JsonRpcError(123, 'Some error message', ['foo' => 'bar']);
        $serialized = serialize($error);
        $this->assertIsString($serialized);
        $unserialized = unserialize($serialized);
        $this->assertInstanceOf(JsonRpcError::class, $unserialized);
        $this->assertEquals(123, $unserialized->getCode());
        $this->assertEquals('Some error message', $unserialized->getMessage());
        $this->assertEquals(['foo' => 'bar'], $unserialized->getData());
    }

    /**
     * Проверяет, что массив может быть правильно использован для создания объекта JsonRpcError,
     * и что его свойства соответствуют значениям в массиве.
     */
    public function testUnserializationFromArray(): void
    {
        $errorData = ['code' => 123, 'message' => 'Some error message', 'data' => ['foo' => 'bar']];
        $error = new JsonRpcError(0, '');
                $error->__unserialize($errorData);
        $this->assertEquals(123, $error->getCode());
        $this->assertEquals('Some error message', $error->getMessage());
        $this->assertEquals(['foo' => 'bar'], $error->getData());
    }

    /**
     * Проверяет, что данные JSON-строки могут быть использованы для создания объекта JsonRpcError,
     * и что его свойства соответствуют значениям в JSON-строке.
     */
    public function testUnserializationFromString(): void
    {
        $errorData = json_encode(['code' => 123, 'message' => 'Some error message', 'data' => ['foo' => 'bar']]);
        $error = new JsonRpcError(0, '');
        $error->__unserialize($errorData);
        $this->assertEquals(123, $error->getCode());
        $this->assertEquals('Some error message', $error->getMessage());
        $this->assertEquals(['foo' => 'bar'], $error->getData());
    }

    /**
     * Проверяет, что выбрасывается исключение, когда недопустимые данные JSON используются для
     * создания объекта JsonRpcError.
     */
    public function testUnserializationFromStringInvalidJson(): void
    {
        $errorData = 'not_a_json';
        $error = new JsonRpcError(0, '');
        $this->expectException(JsonRpcException::class);
        $error->__unserialize($errorData);
    }

    /**
     * Проверяет, что нулевое значение для свойства данных в JSON-строке правильно обрабатывается
     * объектом JsonRpcError.
     */
    public function testUnserializationFromStringNullData(): void
    {
        $errorData = json_encode(['code' => 123, 'message' => 'Some error message','data' => null]);
        $error = new JsonRpcError(0, '');
        $error->__unserialize($errorData);
        $this->assertEquals(null, $error->getData());
    }

    /**
     * Проверяет метод composeArray класса JsonRpcError. Он убеждается в том, что метод возвращает
     * ожидаемый набор данных об ошибке в формате JSON-RPC.
     */
    public function testRpcErrorComposeArray()
    {
        $error = new JsonRpcError(100, "Test error message", ["key" => "value"]);
        $expectedResult = [
            "code" => 100,
            "message" => "Test error message",
            "data" => ["key" => "value"]
        ];

        $this->assertSame($expectedResult, $error->composeArray());
    }

    /**
     * Проверяет корректность возвращаемого методом getJsonRequest JSON-RPC-запроса
     */
    public function testRpcErrorGetJsonRequest()
    {
        $error = new JsonRpcError(100, "Test error message", ["key" => "value"]);
        $expectedResult = '{"code":100,"message":"Test error message","data":{"key":"value"}}';

        $this->assertSame($expectedResult, $error->getJsonRequest());
    }
}
