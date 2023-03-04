<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class AlterUrlOnContents extends BcMigration
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
        $table = $this->table('contents');
        $table->changeColumn('url', 'text', [
            'default' => null,
            'null' => true,
        ]);
        $table->update();
    }
}
