<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class Initial extends BcMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     * @return void
     */
    public function up(): void
    {
        $this->table('seo_metas')
            ->addColumn('modified', 'datetime', [
                'comment' => '更新日時',
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => '作成日時',
                'null' => true,
            ])
            ->addColumn('table_alias', 'string', [
                'comment' => 'テーブル',
                'limit' => 255,
            ])
            ->addColumn('table_id', 'integer', [
                'comment' => 'テーブルID',
            ])
            ->addColumn('entity_id', 'integer', [
                'comment' => 'エンティティID',
            ])
            ->addColumn('og_title', 'string', [
                'comment' => 'OG タイトル',
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('og_description', 'string', [
                'comment' => 'OG ディスクリプション',
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('og_url', 'string', [
                'comment' => 'OG URL',
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('og_type', 'string', [
                'comment' => 'OG タイプ',
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('og_image', 'string', [
                'comment' => 'OG イメージ',
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('description', 'string', [
                'comment' => 'ディスクリプション',
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('keywords', 'string', [
                'comment' => 'キーワード',
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('canonical_url', 'string', [
                'comment' => 'カノニカル URL',
                'limit' => 255,
                'null' => true,
            ])
            ->addIndex(['table_alias', 'table_id', 'entity_id'], ['unique' => true])
            ->create();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-down-method
     * @return void
     */
    public function down(): void
    {
        $this->table('seo_metas')->drop()->save();
    }
}
