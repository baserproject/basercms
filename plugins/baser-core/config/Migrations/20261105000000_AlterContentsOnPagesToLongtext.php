<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;
use Cake\Database\Driver\Mysql;
use Migrations\Db\Adapter\MysqlAdapter;

/**
 * pages.contents を LONGTEXT に変更する
 *
 * 作成時の limit 4294967295 が、cakephp/migrations の builtin バックエンドでは TEXT として扱われ、
 * 本文が 64KB を超えると保存できなかったため。TEXT の上限があるのは MySQL のみのため、MySQL の場合だけ変更する。
 */
class AlterContentsOnPagesToLongtext extends BcMigration
{
    /**
     * up
     * @return void
     */
    public function up()
    {
        if (!$this->getAdapter()->getConnection()->getDriver() instanceof Mysql) return;
        $this->table('pages')
            ->changeColumn('contents', 'text', [
                'default' => null,
                'limit' => MysqlAdapter::TEXT_LONG,
                'null' => true,
            ])
            ->update();
    }

    /**
     * down
     * @return void
     */
    public function down()
    {
        // 何もしない
    }
}
