<?php

namespace radarx;

class Payload
{
    private static const NOT_AUTHENTICATED = -1;
    private static const NOT_AUTHORIZED = -2;
    private static const NOT_VALIDATED = -3;
    private static const FAILURE = 0;
    private static const SUCCESS = 1;

    protected $status;

    protected $target;

    protected $input;

    protected $output;

    protected $errors;

    protected function __construct($status, $target, $output, $errors)
    {
        $this->status = $status;
        $this->target = $target;
        $this->input = null;
        $this->output = $output;
        $this->errors = $errors;
    }

    public static function notAuthenticated(): Payload
    {
        return new Payload(self::NOT_AUTHENTICATED, null, null, null);
    }

    public static function notAuthorized($target): Payload
    {
        return new Payload(self::NOT_AUTHORIZED, $target, null, null);
    }

    public static function notValidated($target, $errors): Payload
    {
        return new Payload(self::NOT_VALID, $target, null, $errors);
    }

    public static function failure($target, $errors): Payload
    {
        return new Payload(self::FAILURE, $target, null, $errors);
    }

    public static function success($target, $output): Payload 
    {
        return new Payload(self::SUCCESS, $target, $output, null);
    }

    public function setInput(array $input): void
    {
        $this->input = $input;
    }

    public function isNotAuthenticated(): bool
    {
        return ($this->status == self::NOT_AUTHENTICATED);
    }

    public function isNotAuthorized(): bool
    {
        return ($this->status == self::NOT_AUTHORIZED);
    }

    public function isNotValid(): bool
    {
        return ($this->status == self::NOT_VALID);
    }

    public function isFailure(): bool
    {
        return ($this->status == self::FAILURE);
    }

    public function isSuccess(): bool
    {
        return ($this->status == self::SUCCESS);
    }

    public function getTarget() 
    {
        return $this->target;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}