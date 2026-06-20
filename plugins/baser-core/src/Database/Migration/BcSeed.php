<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Database\Migration;

use BaserCore\Utility\BcUtil;
use Migrations\BaseSeed;
use Migrations\Db\Table;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * BcSeed
 *
 * CakePHP Migrations 4.5 以降、AbstractSeed は非推奨となり BaseSeed が推奨されたため移行。
 */
class BcSeed extends BaseSeed
{

    /**
     * テーブルを取得する
     *
     * プレフィックスをテーブル名に反映する
     *
     * @param string $tableName
     * @param array $options
     * @return Table
     * @checked
     * @noTodo
     * @unitTest
     */
    public function table(string $tableName, array $options = []): Table
    {
        // BaseSeed では $this->input が無いため、実行中アダプターの接続からプレフィックスを取得する
        $prefix = $this->getAdapter()->getConnection()->config()['prefix'] ?? '';
        return parent::table($prefix . $tableName, $options);
    }

}
