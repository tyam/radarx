<?php
/**
 * CsrfTokenHandler
 * 
 * psr-7 compliant CSRF token handler middleware
 */

namespace tyam\radarx;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class CsrfTokenHandler 
{
    /**
     * @var session place to store a csrf token string.
     */
    private $session;

    /**
     * @var excludes array list of excluded routes.
     *
     * exludes := [exclude, ...]
     * exlude := [method, path]
     * method := PCRE
     * path := PCRE
     */
    private $excludes;

    public function __construct(CsrfTokenHolder $session, array $excludes = null)
    {
        if (is_null($excludes)) {
            $excludes = [['/(POST|PUT|PATCH|DELETE)/i', '/.*/i']];
        }

        $this->session = $session;
        $this->excludes = $excludes;
    }

    protected function testToCheck(Request $request)
    {
        foreach ($this->excludes as $exclude) {
            list($method, $path) = $exclude;
            if (preg_match($method, $request->getMethod()) && 
                preg_match($path, $request->getRequestTarget())) {
                return true;
            }
        }
        return false;
    }

    protected function getArrivedCsrfToken(Request $request)
    {
        $post = $request->getParsedBody();
        if (isset($post['_csrf_token'])) {
            return $post['_csrf_token'];
        }
        return '';
    }

    protected function generateCsrfToken()
    {
        return md5(microtime());
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        if ($this->testToCheck($request)) {
            $held = $this->session->getCsrfToken();
            $arrived = $this->getArrivedCsrfToken($request);

            if ($held !== $arrived) {
                throw new \RuntimeException('csrf token unmatched: '.$held.", ".$arrived);
            }
        }
        
        if (! $this->session->hasCsrfToken()) {
            $token = $this->generateCsrfToken();
            $this->session->setCsrfToken($token);
        }

        return $next($request, $response);
    }
}