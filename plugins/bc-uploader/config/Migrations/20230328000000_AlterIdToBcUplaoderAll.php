<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;
use Phinx\Db\Adapter\PostgresAdapter;

class AlterIdToBcUploaderAll extends BcMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $this->table('uploader_files')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('uploader_categories')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('uploader_configs')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
    }

}
