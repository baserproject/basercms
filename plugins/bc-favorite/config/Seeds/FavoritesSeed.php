<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcSeed;

/**
 * Favorites seed.
 */
class FavoritesSeed extends BcSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'id' => '1',
                'user_id' => '1',
                'name' => 'クレジット',
                'url' => 'javascript:$.bcCredit.show();',
                'sort' => '1',
                'created' => NULL,
                'modified' => NULL,
            ]
        ];

        $table = $this->table('favorites');
        $table->insert($data)->save();
    }
}
