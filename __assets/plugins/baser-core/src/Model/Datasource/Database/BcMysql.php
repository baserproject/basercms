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
namespace BaserCore\Model\Datasource\Database;

use Cake\Database\Driver\Mysql;

/**
 * Class BcMysql
 *
 * MySQL DBO拡張
 *
 */
class BcMysql extends Mysql
{
// COSTOMIZE ADD 2014/07/02 ryuring
// >>>
    /**
     * テーブル名のリネームステートメントを生成
     *
     * @param string $sourceName
     * @param string $targetName
     * @return string
     */
    public function buildRenameTable($sourceName, $targetName)
    {
        return "ALTER TABLE `" . $sourceName . "` RENAME `" . $targetName . "`";
    }
// <<<
}
