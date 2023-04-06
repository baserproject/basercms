<?php

namespace BaserCore\View\Helper;

/**
 * BcPluginBaserHelperInterface
 *
 * BcBaserHelper 経由で透過的に別のヘルパーを呼び出すための
 * ヘルパーを定義するためのインターフェイス
 *
 * 実装するクラスでは、helpers プロパティを定義しておくこと
 */
interface BcPluginBaserHelperInterface
{

    /**
     * メソッドリスト
     *
     * return [
     *  '呼び出しメソッド' => ['呼び出し先のヘルパ名', '呼び出し先のメソッド']
     * ];
     *
     * @return array
     */
    public function methods(): array;

}
