<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class TwoFactorAuthentications extends AbstractMigration
{
    public function up(): void
    {
         $this->table('two_factor_authentications', [
            'collation' => 'utf8mb4_general_ci'
         ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('code', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('is_verified', 'boolean', [
                'default' => false,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();
    }
}
