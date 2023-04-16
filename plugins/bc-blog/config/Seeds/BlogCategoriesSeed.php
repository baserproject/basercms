<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcSeed;

/**
 * BlogCategories seed.
 */
class BlogCategoriesSeed extends BcSeed
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
                'id' => 1,
                'blog_content_id' => 1,
                'no' => 1,
                'name' => 'release',
                'title' => 'プレスリリース',
                'status' => true,
                'parent_id' => NULL,
                'lft' => 1,
                'rght' => 2,
                'created' => NULL,
                'modified' => NULL,
            ],
        ];

        $table = $this->table('blog_categories');
        $table->insert($data)->save();
    }
}
