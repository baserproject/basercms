<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class CreateMailMessage1 extends BcMigration
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

        $this->table('mail_message_1', [
            'collation' => 'utf8mb4_general_ci'
         ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('name_1', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('name_2', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('sex', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('email_1', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('email_2', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('tel', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('zip', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('address_1', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('address_2', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('address_3', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('category', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('message', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('root', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();
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
        $this->table('mail_message_1')->drop()->save();
    }
}
