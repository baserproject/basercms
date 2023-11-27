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
use Cake\Datasource\ConnectionManager;
use Migrations\AbstractSeed;
use Phinx\Db\Table;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * BcSeed
 */
class BcSeed extends AbstractSeed
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
     */
    public function table(string $tableName, array $options = []): Table
    {
        $prefix = ConnectionManager::get($this->input->getOption('connection'))->config()['prefix'];
        return parent::table($prefix . $tableName);
    }

}
