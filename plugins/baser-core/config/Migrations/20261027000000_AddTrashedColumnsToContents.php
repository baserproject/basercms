<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class AddTrashedColumnsToContents extends BcMigration
{
    /**
     * Change Method.
     *
     * ゴミ箱から元に戻す際に、元の親フォルダと公開状態に戻せるよう、
     * ゴミ箱に入れる前の値を保持するカラムを追加する
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('contents');
        $table->addColumn('trashed_parent_id', 'integer', [
            'after' => 'deleted_date',
            'null' => true,
            'default' => null,
        ]);
        $table->addColumn('trashed_self_status', 'boolean', [
            'after' => 'trashed_parent_id',
            'null' => true,
            'default' => null,
        ]);
        $table->update();
    }
}
