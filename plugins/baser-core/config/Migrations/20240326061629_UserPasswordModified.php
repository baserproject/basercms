<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class UserPasswordModified extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('password_modified', 'datetime', [
            'after' => 'nickname',
            'null' => true,
        ]);
        $table->update();
    }
}
