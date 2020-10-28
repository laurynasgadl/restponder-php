<?php


namespace Luur\Restponder\Tests;


use Exception;
use Luur\Restponder\ErrorData;
use Luur\Restponder\Response;
use Luur\Restponder\Restponder;
use PHPUnit\Framework\TestCase;

class RestponderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Restponder::reset();
    }

    public function test_builds_response()
    {
        $response = Restponder::response(true);

        self::assertInstanceOf(Response::class, $response);
        self::assertTrue($response->getResult());
        self::assertTrue($response->isSuccess());
        self::assertNull($response->getError());
    }

    public function test_sets_default_error_code()
    {
        $code = 987;

        Restponder::setErrorDefaultCode($code);

        $error = new ErrorData();

        self::assertEquals($code, $error->getCode());
    }

    public function test_sets_default_error_message()
    {
        $message = 'default error message';

        Restponder::setErrorDefaultMessage($message);

        $error = new ErrorData();

        self::assertEquals($message, $error->getMessage());
    }

    public function test_sets_include_error_debug()
    {
        $exception = new Exception('test exception', 987);

        Restponder::setErrorIncludeDebug(true);

        $error = new ErrorData($exception);

        self::assertIsArray($error->toArray()[ErrorData::DEBUG_FIELD]);
    }

    public function test_registers_response_handler()
    {
        $response = new Response(true);
        Restponder::registerResponseHandler(Response::class, function (Response $variable, Response $response) {
            $response->setError($variable->getResult());
        });

        $handled = new Response($response);

        self::assertFalse($handled->isSuccess());
    }

    public function test_registers_error_handler()
    {
        $exception = new Exception('test exception', 987);
        Restponder::registerErrorHandler(Exception::class, function (Exception $exception, ErrorData $error) {
            $error->setMessage($exception->getCode().$exception->getMessage());
        });

        $error = new ErrorData($exception);

        self::assertEquals($exception->getCode().$exception->getMessage(), $error->getMessage());
    }

    public function test_resets_configuration()
    {
        Restponder::setErrorDefaultCode(987);
        Restponder::setErrorDefaultMessage('test default message');
        Restponder::setErrorIncludeDebug(true);
        Restponder::registerResponseHandler(Response::class, function (Response $variable, Response $response) {
            $response->setError($variable->getResult());
        });
        Restponder::registerErrorHandler(Exception::class, function (Exception $exception, ErrorData $error) {
            $error->setMessage($exception->getCode().$exception->getMessage());
        });
        Restponder::reset();

        $handled   = new Response(new Response(true));
        $exception = new Exception('test exception', 987);
        $error     = new ErrorData($exception);

        self::assertEquals(0, ErrorData::$defaultCode);
        self::assertEquals('Unexpected error occurred', ErrorData::$defaultMessage);
        self::assertFalse(ErrorData::$includeDebug);
        self::assertTrue($handled->isSuccess());
        self::assertEquals($exception->getMessage(), $error->getMessage());
    }
}
