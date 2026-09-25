<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.3.1
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

/**
 * Trait BcQueryParameterTrait
 *
 * リクエスト由来のクエリパラメータを、ORM へ渡す前に安全化するためのトレイト。
 */
trait BcQueryParameterTrait
{

    /**
     * リクエスト由来のクエリパラメータから ORM 内部構造キーを除去する
     *
     * `contain` / `conditions` / `fields` などの ORM 内部構造は、本来サーバ側で
     * のみ組み立てるものであり、フロントの未信頼入力から受け取ると SQL
     * インジェクションの sink になる（GHSA-ch8f-q957-r9xm / GHSA-2ghw-gwvv-mv56）。
     * 公開フロントのコントローラは、リクエストのクエリパラメータをサービスへ
     * 渡す前に本メソッドを通し、これらのキーを取り除くこと。
     *
     * @param array $queryParams リクエスト由来のクエリパラメータ
     * @return array ORM 内部構造キーを除去したクエリパラメータ
     * @checked
     * @noTodo
     * @unitTest
     */
    public function removeOrmStructure(array $queryParams): array
    {
        $reserved = [
            'contain',
            'conditions',
            'fields',
            'finder',
            'joinType',
            'strategy',
            'matching',
            'notMatching',
            'leftJoinWith',
            'innerJoinWith',
            'select',
            'group',
            'having',
        ];
        return array_diff_key($queryParams, array_flip($reserved));
    }

}
