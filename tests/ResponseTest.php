<?php


namespace Luur\Restponder\Tests;


use Exception;
use Luur\Restponder\ErrorData;
use Luur\Restponder\Response;
use Luur\Restponder\Restponder;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Restponder::reset();
    }

    public function test_creates_object_from_null()
    {
        $response = new Response();

        self::assertInstanceOf(Response::class, $response);
        self::assertTrue($response->isSuccess());
        self::assertNull($response->getResult());
        self::assertNull($response->getError());
        self::assertArrayNotHasKey(Response::METADATA_FIELD, $response->toArray());
    }

    public function test_creates_object_from_exception()
    {
        $exception = new Exception('test exception', 987);

        $response = new Response($exception);

        self::assertFalse($response->isSuccess());
        self::assertNull($response->getResult());
        self::assertInstanceOf(ErrorData::class, $response->getErrorObject());
        self::assertArrayNotHasKey(Response::METADATA_FIELD, $response->toArray());
    }

    public function resultVariableDataProvider(): array
    {
        return [
            [null],
            [true],
            [false],
            [123],
            [123.123],
            ['test'],
            [['some' => 'variable']],
        ];
    }

    /**
     * @dataProvider resultVariableDataProvider
     * @param $variable
     */
    public function test_creates_object_from_variable($variable)
    {
        $response = new Response($variable);

        $requestId = 'abcdefg';
        $response->addMetadata('request_id', $requestId);

        self::assertTrue($response->isSuccess());
        self::assertEquals($variable, $response->getResult());
        self::assertNull($response->getError());
        self::assertEquals(['request_id' => $requestId], $response->getMetadata());
    }

    public function test_creates_object_from_custom_class()
    {
        $custom  = new Exception('test custom', 987);
        $handler = function (Exception $exception, Response $data) {
            $data->setResult($exception->getCode().$exception->getMessage());
            $data->setMetadata(['test' => true]);
        };

        Response::registerHandler(get_class($custom), $handler);

        $response = new Response($custom);

        self::assertTrue($response->isSuccess());
        self::assertEquals($custom->getCode().$custom->getMessage(), $response->getResult());
        self::assertArrayHasKey(Response::METADATA_FIELD, $response->toArray());
        self::assertEquals(['test' => true], $response->toArray()[Response::METADATA_FIELD]);
    }

    public function test_encodes_in_json()
    {
        $result = new Response(new Exception('test exception', 987));

        self::assertEquals('{"success":false,"result":null,"error":{"code":987,"message":"test exception"}}',
            json_encode($result));
    }
}
