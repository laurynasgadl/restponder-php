<?php


namespace Luur\Restponder;


use Throwable;

class ErrorData
{
    use HasCustomHandlers;

    public static $defaultCode    = 0;
    public static $defaultMessage = 'Unexpected error occurred';
    public static $includeDebug   = false;

    public const CODE_FIELD    = 'code';
    public const MESSAGE_FIELD = 'message';
    public const DETAILS_FIELD = 'details';
    public const DEBUG_FIELD   = 'debug';

    protected $data = [];

    public function __construct($error = null)
    {
        $this->data = $this->getDefaultData();

        if (is_object($error) && array_key_exists(get_class($error), self::$customHandlers)) {
            $this->handleCustom($error);
        } else {
            if ($error instanceof Throwable) {
                $this->parseFromException($error);
            } else {
                if (is_array($error)) {
                    $this->parseFromArray($error);
                } else {
                    if (is_string($error)) {
                        $this->setMessage($error);
                    }
                }
            }
        }
    }

    public function getCode(): int
    {
        return $this->data[self::CODE_FIELD];
    }

    public function getMessage(): string
    {
        return $this->data[self::MESSAGE_FIELD];
    }

    public function getDetails(): ?array
    {
        return $this->data[self::DETAILS_FIELD] ?? null;
    }

    public function setCode($code): void
    {
        $this->data[self::CODE_FIELD] = is_int($code) ? $code : self::$defaultCode;
    }

    public function setMessage(?string $message): void
    {
        $this->data[self::MESSAGE_FIELD] = ($message ?? self::$defaultMessage);
    }

    public function setDetails(?array $details): void
    {
        $this->data[self::DETAILS_FIELD] = $details;
    }

    public function addDetail(string $key, $value): void
    {
        if (!array_key_exists(self::DETAILS_FIELD, $this->data)) {
            $this->data[self::DETAILS_FIELD] = [];
        }
        $this->data[self::DETAILS_FIELD][$key] = $value;
    }

    public function setDebug(?array $debug): void
    {
        $this->data[self::DEBUG_FIELD] = $debug;
    }

    protected function parseFromException(Throwable $exception): void
    {
        $this->setMessage($exception->getMessage());
        $this->setCode($exception->getCode());

        if (self::$includeDebug) {
            $this->setDebug([
                'type'  => get_class($exception),
                'trace' => $exception->getTraceAsString(),
            ]);
        }
    }

    protected function parseFromArray(array $data): void
    {
        $this->setCode($data[self::CODE_FIELD] ?? self::$defaultCode);
        $this->setMessage($data[self::MESSAGE_FIELD] ?? self::$defaultMessage);

        if (array_key_exists(self::DETAILS_FIELD, $data)) {
            $this->setDetails($data[self::DETAILS_FIELD]);
        }
    }

    protected function getDefaultData(): array
    {
        return [
            self::CODE_FIELD    => self::$defaultCode,
            self::MESSAGE_FIELD => self::$defaultMessage,
        ];
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public static function resetOptions()
    {
        self::$defaultCode    = 0;
        self::$defaultMessage = 'Unexpected error occurred';
        self::$includeDebug   = false;
        self::resetHandlers();
    }
}
