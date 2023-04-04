<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class AddAutoCompleteMailFields extends BcMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     * @return void
     */
    public function up()
    {
        $this->table('mail_fields')
            ->addColumn('autocomplete', 'string', [
                'after' => 'options',
                'default' => null,
                'null' => true,
            ])
            ->update();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-down-method
     * @return void
     */
    public function down()
    {
        $this->table('mail_fields')
            ->removeColumn('autocomplete')
            ->update();
    }
}
