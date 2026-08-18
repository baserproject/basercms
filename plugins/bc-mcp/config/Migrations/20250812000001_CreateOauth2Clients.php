<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateOauth2Clients extends BaseMigration
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
        $table = $this->table('oauth2_clients');
        $table->addColumn('client_id', 'string', [
            'default' => null,
            'limit' => 80,
            'null' => false,
        ]);
        $table->addColumn('client_secret', 'string', [
            'default' => null,
            'limit' => 80,
            'null' => true,
        ]);
        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => false,
        ]);
        $table->addColumn('redirect_uris', 'text', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('grants', 'text', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('scopes', 'text', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('is_confidential', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addColumn('registration_access_token', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addPrimaryKey(['id']);
        $table->addIndex(['client_id'], ['unique' => true]);
        $table->create();
    }
}
