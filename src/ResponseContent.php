<?php


namespace Luur\Restponder;


use Exception;
use JsonSerializable;
use Throwable;

class ResponseContent implements JsonSerializable
{
    use HasCustomHandlers;

    public const SUCCESS_FIELD  = 'success';
    public const RESULT_FIELD   = 'result';
    public const ERROR_FIELD    = 'error';
    public const METADATA_FIELD = 'metadata';

    protected $content = [];

    public function __construct($response = null)
    {
        $this->content = $this->getDefaultContent();

        if (is_object($response) && array_key_exists(get_class($response), self::$customHandlers)) {
            $this->handleCustom($response);
        } else {
            if ($response instanceof Throwable || $response instanceof ErrorData) {
                $this->setError($response);
            } else {
                $this->setResult($response);
            }
        }
    }

    public function setResult($result): void
    {
        $this->content[self::RESULT_FIELD] = $result;
    }

    public function setError($error): void
    {
        if (!$error instanceof ErrorData) {
            $error = new ErrorData($error);
        }

        $this->content[self::ERROR_FIELD] = $error;
    }

    public function setMetadata(?array $metadata): void
    {
        $this->content[self::METADATA_FIELD] = $metadata;
    }

    public function addMetadata(string $key, $value): void
    {
        if (!array_key_exists(self::METADATA_FIELD, $this->content)) {
            $this->content[self::METADATA_FIELD] = [];
        }

        $this->content[self::METADATA_FIELD][$key] = $value;
    }

    public function isSuccess(): bool
    {
        return !$this->getError();
    }

    public function getResult()
    {
        return ($this->content[self::RESULT_FIELD] ?? null);
    }

    public function getError(): ?array
    {
        return $this->getErrorObject() ? $this->getErrorObject()->toArray() : null;
    }

    public function getErrorObject(): ?ErrorData
    {
        return ($this->content[self::ERROR_FIELD] ?? null);
    }

    public function getMetadata(): ?array
    {
        return ($this->content[self::METADATA_FIELD] ?? null);
    }

    protected function getDefaultContent(): array
    {
        return [
            self::RESULT_FIELD => null,
            self::ERROR_FIELD  => null,
        ];
    }

    public function toArray(): array
    {
        $array                    = $this->content;
        $array[self::ERROR_FIELD] = $this->getError();

        return [self::SUCCESS_FIELD => $this->isSuccess()] + $array;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        try {
            return json_encode($this->jsonSerialize());
        } catch (Exception $exception) {
            return '';
        }
    }
}
