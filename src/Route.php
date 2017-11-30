<?php
/**
 * Route
 *
 * radar標準のRouteの拡張。
 * ルート名{name}に対して、InputをWeb\{name}Input、ResponderをWeb\{name}Responderに解決する。
 * tyam\radarxに移動のこと。
 */

namespace Web;

use Radar\Adr\Route as BaseRoute;

class Route extends BaseRoute {
     public function name($name)
     {
        parent::name($name);

        $input = 'Web\\' . $this->name . 'Input';
        if (class_exists($input)) {
            $this->input($input);
        }

        $responder = 'Web\\' . $this->name . 'Responder';
        if (class_exists($responder)) {
            $this->responder($responder);
        }

        return $this;
    }
}