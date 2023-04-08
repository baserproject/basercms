<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class RemoveCodeFromPages extends BcMigration
{
    /**
     * up
     * @return void
     */
    public function up()
    {
        $table = $this->table('pages');
        $table->removeColumn('code')
            ->save();
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
