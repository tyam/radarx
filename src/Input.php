<?php
/**
 * ConsoleInput
 * 
 * コンソール固有のInput。
 * ルートにパラメータがあれば、それを出現順にリストアップし、最後にフォームを付加する。
 * フォームは、メソッドがHEAD、GET、DELETEの場合はクエリ文字列を、
 * メソッドがPOST、PATCH、PUTの場合はparsedBodyとする。
 * たとえば、GET /comment/3/12?format=jsonの場合であれば、抽出される入力は
 * `[3, 12, ['format' => 'json']]`
 * となる。
 */

namespace tyam\radarx;

use Psr\Http\Message\ServerRequestInterface as Request;
use tyam\fadoc\Converter;

class Input
{
    private $converter;

    public function __construct(Converter $converter) 
    {
        $this->converter = $converter;
    }

    public function __invoke(Request $request)
    {
        $args = $this->collectParameters($request);
        $form = $this->collectForm($request);
        $args[] = $form;
        $args[] = new PayloadFactory($form);
        return $args;
    }

    protected function collectform(Request $request)
    {
        switch (strtoupper($request->getMethod())) {
            case 'HEAD': 
            case 'GET': 
                return $request->getQueryParams();
            case 'POST': 
            case 'PATCH':
            case 'PUT': 
                return $request->getParsedBody();
            default: 
                return [];
        }
    }

    protected function collectDomainMethod(Request $request)
    {
        $domain = $request->getAttribute('radar/adr:route')->domain;
        $c = new ReflectionClass($domain);
        return $c->getMethod('__invoke');
    }

    protected function collectParameters(Request $request)
    {
        $mref = $this->collectDomainMethod($request);
        $ps = $mref->getParameters();
        $nps = count($ps);
        $params = [];

        // リンク層のメソッドのパラメータは($param1, $param2, ..., $form, $payloadFactory)。
        // ここでは$paramNを集めるので後ろの2つは除外する。
        for ($i = 0; $i < $nps - 2; $i++) {
            $p = $ps[$i];
            $val = $request->getAttribute(''.$i);
            $params[] = $this->promoteParameter($p, $val);
        }

        return $params;
    }

    protected function promoteParameter($p, $v)
    {
        $form = $this->embedParameter($p, $v);
        $cd = $this->converter->objectise([$p->getClass()->getName(), '__construct'], $form);
        if ($cd()) {
            return $cd->get();
        } else {
            return false;
        }
    }

    protected function embedParameter($p, $v)
    {
        if ($p->getClass()) {
            $c = $p->getClass();
            $ctr = $c->getConstructor($c);
            $ps = $ctr->getParameters();
            $binding = $this->promoteParameter($ps[0], $v);
            return [$p->getName() => $binding];
        } else {
            return [$p->getName() => $v];
        }
    }
}