<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;
use Cake\Database\Driver\Mysql;
use Migrations\Db\Adapter\MysqlAdapter;

/**
 * blog_posts の本文・詳細（下書きを含む）を LONGTEXT に変更する
 *
 * 作成時の limit 4294967295 が、cakephp/migrations の builtin バックエンドでは TEXT として扱われ、
 * 本文が 64KB を超えると保存できなかったため。TEXT の上限があるのは MySQL のみのため、MySQL の場合だけ変更する。
 */
class AlterContentsOnBlogPostsToLongtext extends BcMigration
{
    /**
     * up
     * @return void
     */
    public function up()
    {
        if (!$this->getAdapter()->getConnection()->getDriver() instanceof Mysql) return;
        $table = $this->table('blog_posts');
        foreach(['content', 'detail', 'content_draft', 'detail_draft'] as $column) {
            $table->changeColumn($column, 'text', [
                'default' => null,
                'limit' => MysqlAdapter::TEXT_LONG,
                'null' => true,
            ]);
        }
        $table->update();
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
