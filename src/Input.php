<?php
/**
 * Input
 * 
 * 汎用Input。
 * アクションメソッド（リンク層のオブジェクトの、典型的には__invoke()）に合った入力を見繕う。
 * アクションメソッドの最後の引数はPayloadFactoryなので、作って渡す。
 * アクションメソッドの後ろから2番目の引数はフォーム（ユーザ入力の配列）なので、HTTPメソッド等を解析して適切な配列を作って渡す。
 * アクションメソッドの他の引数はURLパラメータで指定されるものなので、ルートで指定された同名のパラメータを読み取り、アクションメソッドが指定する型に変換して渡す。
 */

namespace tyam\radarx;

use Psr\Http\Message\ServerRequestInterface as Request;
use tyam\fadoc\Converter;
use tyam\radarx\PayloadFactory;

class Input
{
    private $converter;

    public function __construct(Converter $converter) 
    {
        $this->converter = $converter;
    }

    public function __invoke(Request $request)
    {
        $args = $this->collectArgs($request);
        $cnt = count($args);
        $form = $this->collectForm($request);
        $args[$cnt - 2] = $form;
        $args[$cnt - 1] = new PayloadFactory($form);
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

    protected function resolveDomainMethod(Request $request)
    {
        $domain = $request->getAttribute('radar/adr:route')->domain;
        if (! $domain) {
            // domain not specified
            throw new \RuntimeException('domain not specified.');
        }
        if (is_array($domain)) {
            $cname = $domain[0];
            $mname = $domain[1];
        } else {
            $cname = $domain;
            $mname = '__invoke';
        }
        return [$cname, $mname];
    }

    protected function collectArgs(Request $request)
    {
        list($cname, $mname) = $this->resolveDomainMethod($request);
        $cref = new \ReflectionClass($cname);
        $mref = $cref->getMethod($mname);

        $ps = $mref->getParameters();
        $nps = count($ps);
        $params = [];

        // リンク層のメソッドのパラメータは($param1, $param2, ..., $form, $payloadFactory)。
        // ここでは$paramNを集めるので後ろの2つは除外する。
        for ($i = 0; $i < $nps - 2; $i++) {
            $p = $ps[$i];
            $name = $p->getName();
            $val = $request->getAttribute($name);
            $params[$i] = $val;
        }

        // 残り2つの引数をダミーで埋める。
        $params[$nps - 2] = [];  // array
        $params[$nps - 1] = [[]];  // new PayloadFactory(array)

        // Converter::REPAIRフラッグを追加して変換すると、可逆性よりも「変換を成功させること」を優先してくれる。
        $cd = $this->converter->objectize([$cname, $mname], $params, Converter::REPAIR);
        if (! $cd()) {
            // ルートの記載と、アクションメソッドの仕様に齟齬がある可能性が高い。
            throw new \Exception('failed to call action method.  Potential inconsistency between a route definition and a domain-action definition: '.$cd->describe());
        }
        return $cd->get();
    }
}