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
    public function run()
    {
        $data = [
            [
                'id' => '1',
                'user_id' => '1',
                'name' => 'クレジット',
                'url' => 'javascript:credit();',
                'sort' => '1',
                'created' => '2020-12-03 14:41:39',
                'modified' => '2020-12-03 14:41:39',
            ],
            [
                'id' => '2',
                'user_id' => '1',
                'name' => 'カスタムフィールドプラグイン 管理',
                'url' => '/admin/cu_custom_field/cu_custom_field_configs/',
                'sort' => '2',
                'created' => '2020-12-04 12:38:26',
                'modified' => '2020-12-04 12:38:26',
            ],
            [
                'id' => '3',
                'user_id' => '1',
                'name' => '静的HTML出力プラグイン 管理',
                'url' => '/admin/cu_static/cu_statics/',
                'sort' => '3',
                'created' => '2020-12-04 12:55:05',
                'modified' => '2020-12-04 12:55:05',
            ],
        ];

        $table = $this->table('favorites');
        $table->insert($data)->save();
    }
}
