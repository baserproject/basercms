<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Favorites seed.
 */
class FavoritesSeed extends AbstractSeed
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
                'created' => '2020-12-03 14:41:39',
                'modified' => '2020-12-03 14:41:39',
            ]
        ];

        $table = $this->table('favorites');
        $table->insert($data)->save();
    }
}
