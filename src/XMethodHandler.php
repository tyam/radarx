<?php
/**
 * XMethodHandler
 *
 * relay/middleware実装。ブラウザから任意のHTTPメソッドを実行できるようにする。
 * 具体的には、X-METHODヘッダがセットされていればそのメソッドとして扱い、
 * またリクエストが$_POST['__METHOD']を含む場合はそのメソッドとして扱う。
 */

namespace tyam\radarx;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class XMethodHandler
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $xmethod = $this->detectMethod($request);
        if ($xmethod !== $request->getMethod()) {
            $request = $request->withMethod($xmethod);
        }
        return $next($request, $response);
    }

    protected function detectMethod(Request $request)
    {
        if ($request->hasHeader('X-METHOD')) {
            return $request->getHeader('X-METHOD')[0];
        }

        $post = $request->getParsedBody();
        if (isset($post['__METHOD'])) {
            return $post['__METHOD'];
        }

        return $request->getMethod();
    }
}