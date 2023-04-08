<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class AddStatusToUsers extends BcMigration
{
    /**
     * up
     * @return void
     */
    public function up()
    {
        $table = $this->table('users');
        $table->addColumn('status', 'boolean', [
            'default' => true,
            'null' => true,
        ]);
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
