# Restponder
Build a better REST API response.

## Response examples


## Usage

### Response data
#### Custom object handler
You can register a custom object handler, to be able to parse response data
from any kind of object, however you like.

```php
use Luur\Restponder\Response;
use Luur\Restponder\Restponder;

$handler = function (Exception $exception, Response $response) {
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
