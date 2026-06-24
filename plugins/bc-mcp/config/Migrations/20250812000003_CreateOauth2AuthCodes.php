<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateOauth2AuthCodes extends BaseMigration
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
        $table = $this->table('oauth2_auth_codes');
        $table->addColumn('code', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => false,
        ]);
        $table->addColumn('user_id', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => false,
        ]);
        $table->addColumn('client_id', 'string', [
            'default' => null,
            'limit' => 80,
            'null' => false,
        ]);
        $table->addColumn('redirect_uri', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('scopes', 'text', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('revoked', 'boolean', [
            'default' => false,
            'null' => false,
        ]);
        $table->addColumn('expires_at', 'datetime', [
            'default' => null,
            'null' => false,
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
        $table->addIndex(['code'], ['unique' => true]);
        $table->addIndex(['client_id']);
        $table->addIndex(['user_id']);
        $table->create();
    }
}
