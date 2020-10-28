<?php


namespace Luur\Restponder\Tests;


use Exception;
use Luur\Restponder\ErrorData;
use Luur\Restponder\Restponder;
use PHPUnit\Framework\TestCase;

class ErrorDataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Restponder::reset();
    }

    public function test_creates_object_from_null()
    {
        $defaultCode    = 987;
        $defaultMessage = 'test default message';

        ErrorData::$defaultCode    = $defaultCode;
        ErrorData::$defaultMessage = $defaultMessage;

        $data = new ErrorData();

        self::assertInstanceOf(ErrorData::class, $data);
        self::assertEquals($defaultCode, $data->getCode());
        self::assertEquals($defaultMessage, $data->getMessage());
        self::assertArrayNotHasKey(ErrorData::DEBUG_FIELD, $data->toArray());
    }

    public function test_creates_object_from_exception()
    {
        $exception = new Exception('test exception', 987);

        ErrorData::$includeDebug = true;

        $data = new ErrorData($exception);

        self::assertEquals($exception->getCode(), $data->getCode());
        self::assertEquals($exception->getMessage(), $data->getMessage());
        self::assertArrayHasKey('debug', $data->toArray());
        self::assertEquals([
            'type'  => get_class($exception),
            'trace' => $exception->getTraceAsString(),
        ], $data->toArray()[ErrorData::DEBUG_FIELD]);
    }

    public function test_creates_object_from_array()
    {
        $array = [
            ErrorData::CODE_FIELD => 987,
            ErrorData::MESSAGE_FIELD => 'test array exception',
            ErrorData::DETAILS_FIELD => ['some kind of details about the exception'],
        ];

        $data = new ErrorData($array);

        self::assertEquals($array[ErrorData::CODE_FIELD], $data->getCode());
        self::assertEquals($array[ErrorData::MESSAGE_FIELD], $data->getMessage());
        self::assertEquals($array[ErrorData::DETAILS_FIELD], $data->getDetails());
        self::assertArrayNotHasKey(ErrorData::DEBUG_FIELD, $data->toArray());
    }

    public function test_creates_object_from_string()
    {
        $string = 'test string exception';

        $data = new ErrorData($string);

        self::assertEquals(ErrorData::$defaultCode, $data->getCode());
        self::assertEquals($string, $data->getMessage());
        self::assertArrayNotHasKey(ErrorData::DEBUG_FIELD, $data->toArray());
    }

    public function test_creates_object_from_custom_class()
    {
        $custom  = new Exception('test custom', 987);
        $handler = function (Exception $exception, ErrorData $data) {
            $data->setMessage($exception->getCode().$exception->getMessage());
            $data->addDetail('test', true);
        };

        ErrorData::registerHandler(get_class($custom), $handler);

        $data = new ErrorData($custom);

        self::assertEquals(ErrorData::$defaultCode, $data->getCode());
        self::assertEquals($custom->getCode().$custom->getMessage(), $data->getMessage());
        self::assertArrayHasKey(ErrorData::DETAILS_FIELD, $data->toArray());
        self::assertEquals(['test' => true], $data->toArray()[ErrorData::DETAILS_FIELD]);
    }
}
