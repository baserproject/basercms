<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcSeed;

/**
 * BlogPostsBlogTags seed.
 */
class BlogPostsBlogTagsSeed extends BcSeed
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
                'blog_post_id' => 2,
                'blog_tag_id' => 1,
                'created' => NULL,
                'modified' => NULL,
            ],
        ];

        $table = $this->table('blog_posts_blog_tags');
        $table->insert($data)->save();
    }
}
