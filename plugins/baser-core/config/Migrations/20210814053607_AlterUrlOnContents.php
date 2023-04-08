<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class AlterUrlOnContents extends BcMigration
{
    /**
     * up
     * @return void
     */
    public function up()
    {
        $table = $this->table('contents');
        $table->changeColumn('url', 'text', [
            'default' => null,
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
