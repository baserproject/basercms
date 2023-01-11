<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * BlogTags seed.
 */
class BlogTagsSeed extends AbstractSeed
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
                'name' => '新製品',
                'created' => '2022-10-01 09:00:00',
                'modified' => NULL,
            ],
        ];

        $table = $this->table('blog_tags');
        $table->insert($data)->save();
    }
}
