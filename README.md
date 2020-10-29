# Restponder
Build a better REST API response.

## Response examples

#### Successful
```php
use Luur\Restponder\Restponder;

$response = Restponder::content('happy little 🌳');
```
```json
{
    "success":true,
    "result":"happy little \ud83c\udf33",
    "error":null
}
```

#### Successful with attached metadata
```php
use Luur\Restponder\Restponder;

$response = Restponder::content('happy little 🌳');
$response->addMetadata('request_id', '1234-5678');
```
```json
{
    "success":true,
    "result":"happy little \ud83c\udf33",
    "error":null,
    "metadata":{
        "request_id":"1234-5678"
    }
}
```

#### Failed
```php
use Luur\Restponder\Restponder;

$response = Restponder::content(new Exception('Oops', 987));
```
```json
{
    "success":false,
    "result":null,
    "error":{
        "code":987,
        "message":"Oops"
    }
}
```

#### Failed with debug included
```php
use Luur\Restponder\Restponder;

Restponder::setErrorIncludeDebug(true);
$response = Restponder::content(new Exception('Oops', 987));
```
```json
{
    "success":false,
    "result":null,
    "error":{
        "code":987,
        "message":"Oops",
        "debug":{
            "type":"Exception",
            "trace":"#0 ..."
        }
    }
}
```

#### Failed with attached details
```php
use Luur\Restponder\ErrorData;
use Luur\Restponder\Restponder;

$handler = function (Exception $exception, ErrorData $data) {
    $data->addDetail('is_validation_exception', $exception instanceof ValidationException);
};

Restponder::registerErrorHandler(Exception::class, $handler);
$response = Restponder::content(new Exception('Oops', 987));
```
```json
{
    "success":false,
    "result":null,
    "error":{
        "code":0,
        "message":"Unexpected error occurred",
        "details":{
            "is_validation_exception":false
        }
    }
}
```

## Usage

### Response data
#### Custom object handler
You can register a custom object handler, to be able to parse response data
from any kind of object, however you like.

```php
use Luur\Restponder\ResponseContent;
use Luur\Restponder\Restponder;

$handler = function (Exception $exception, ResponseContent $response) {
    $response->addMetadata('failed', true);
    $response->addMetadata('error_message', $exception->getMessage());
};

Restponder::registerResponseHandler(Exception::class, $handler);
```

### Error data
#### Custom object handler
You can register a custom object handler, just like with `Response`.

```php
use Luur\Restponder\ErrorData;
use Luur\Restponder\Restponder;

$handler = function (Exception $exception, ErrorData $data) {
    $data->setMessage($exception->getCode().$exception->getMessage());
    $data->addDetail('test', true);
};

Restponder::registerErrorHandler(Exception::class, $handler);
```
