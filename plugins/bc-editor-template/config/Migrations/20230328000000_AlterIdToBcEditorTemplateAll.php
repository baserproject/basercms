<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;
use Phinx\Db\Adapter\PostgresAdapter;

class AlterIdToBcEditorTemplateAll extends BcMigration
{
    /**
     * up
     * @return void
     */
    public function up()
    {
        $this->table('editor_templates')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
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
