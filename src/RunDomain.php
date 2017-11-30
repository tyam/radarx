<?php
/**
 * RunDomain
 *
 * プレゼンテーション層からドメイン処理を呼び出したいときに使う。
 * radarではアプリケーション層がドメイン層のインターフェイスになっていて、
 * またアプリケーション層のコードはDIにより注入も行われるので、直接
 * ドメイン処理を呼び出すことはできない。
 * なのでこのトレイトを使う。
 * このトレイトはsetterインジェクションで使うことを想定している。
 * tyam\radarxに移動のこと。
 *
 */

namespace Custom;

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
        $domain = $this->resolve($spec);
        $result = call_user_func_array($domain, array $args);
        return $result;
    }
}