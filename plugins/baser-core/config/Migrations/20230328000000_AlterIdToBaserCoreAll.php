<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;
use Phinx\Db\Adapter\PostgresAdapter;

class AlterIdToBaserCoreAll extends BcMigration
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
        $this->table('pages')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('password_requests')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('content_folders')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('plugins')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('user_groups')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('users')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('users_user_groups')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('login_stores')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('site_configs')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('dblogs')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('permissions')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('sites')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('contents')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('permission_groups')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
    }

}
