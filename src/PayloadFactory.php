<?php
/**
 * PayloadFactory
 *
 * Payloadを作成するsnippetのクラス。
 * 「どのオブジェクトが（extras）、どんな引数で（input）、何をして、どうなった（output）」になっている。
 */

namespace tyam\radarx;

use Aura\Payload\Payload;
use Aura\Payload_Interface\PayloadStatus;

class PayloadFactory
{
    private $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public function notAuthenticated(): Payload
    {
        return (new Payload())->setStatus(PayloadStatus::NOT_AUTHENTICATED)
                              ->setInput($this->input);
    }

    public function notFound(): Payload
    {
        return (new Payload())->setStatus(PayloadStatus::NOT_FOUND)
                              ->setInput($this->input);
    }

    public function notAuthorized($target): Payload
    {
        return (new Payload())->setStatus(PayloadStatus::NOT_AUTHORIZED)
                              ->setInput($this->input)
                              ->setExtras($target);
    }

    public function notValid($target, $errors): Payload
    {
        return (new Payload())->setStatus(PayloadStatus::NOT_VALID)
                              ->setInput($this->input)
                              ->setExtras($target)
                              ->setMessages($errors);
    }

    public function failure($target, $errors): Payload
    {
        return (new Payload())->setStatus(PayloadStatus::FAILURE)
                              ->setInput($this->input)
                              ->setExtras($target)
                              ->setMessages($errors);
    }

    public function success($target, $output): Payload 
    {
        return (new Payload())->setStatus(PayloadStatus::SUCCESS)
                              ->setInput($this->input)
                              ->setExtras($target)
                              ->setOutput($output);
    }
}