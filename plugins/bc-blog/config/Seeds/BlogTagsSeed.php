<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcSeed;

/**
 * BlogTags seed.
 */
class BlogTagsSeed extends BcSeed
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
                'created' => NULL,
                'modified' => NULL,
            ],
        ];

        $table = $this->table('blog_tags');
        $table->insert($data)->save();
    }
}
