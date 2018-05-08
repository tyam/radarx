<?php
/**
 * RunDomain
 *
 * プレゼンテーション層からドメイン処理を呼び出したいときに使うトレイト。
 * radarではアプリケーション層がDIのターゲットにもなりがちなので、直接
 * ドメイン層を呼び出すのはうまくいかないケースがある。
 * ドメイン処理を呼び出したいときの適切なエントリーポイントはアプリ
 * ケーション層だが、これはDIのターゲットでもあるので直接newできない。
 *
 * なのでこのトレイトを使う。
 * このトレイトはDI（aura/di）を介してアプリケーション層のコードを呼び
 * 出すようにできている。
 * 
 * なお、このトレイトはsetterインジェクションで使うことを想定している。
 * `$di['runDomain']['setResolve'] = $di->getResolveHelper();`
 *
 */

namespace tyam\radarx;

trait RunDomain 
{
    private $resolve;

    /**
     * レゾルバをセットする。
     *
     * @param Callable $resolve レゾルバ
     * @return void
     */
    public function setResolve(Callable $resolve) :void
    {
        $this->resolve = $resolve;
    }

    /**
     * ドメイン処理を実行する
     *
     * @param $spec メソッドを表現するオブジェクト。通常はオブジェクトとメソッド名のリスト。
     * @param array $args メソッドの引数
     * @return mixed ドメイン処理の返却値
     */
    public function runDomain($spec, array $args) 
    {
        $resolve = $this->resolve;
        $domain = $resolve($spec);
        $result = call_user_func_array($domain, $args);
        return $result;
    }
}