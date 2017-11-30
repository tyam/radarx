<?php
/**
 * Route
 *
 * Routeの拡張。
 * ルート名{name}に対して、Inputを{dir}\{name}Input、Responderを{dir}\{name}Responderに解決する。
 * {dir}は設定可能。
 */

namespace tyam\radarx;

use Radar\Adr\Route as BaseRoute;

class Route extends BaseRoute {
    private $dir;

    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    public function getDir(): string
    {
        return $this->dir;
    }

    public function name($name)
    {
        parent::name($name);

        $input = $this->dir . '\\' . $this->name . 'Input';
        if (class_exists($input)) {
            $this->input($input);
        }

        $responder = $this->dir . '\\' . $this->name . 'Responder';
        if (class_exists($responder)) {
            $this->responder($responder);
        }

        return $this;
    }
}