<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * BlogCategories seed.
 */
class BlogCategoriesSeed extends AbstractSeed
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
                'created' => '2022-10-01 09:00:00',
                'modified' => NULL,
            ],
        ];

        $table = $this->table('blog_categories');
        $table->insert($data)->save();
    }
}
