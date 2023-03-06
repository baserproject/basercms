<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcSeed;

/**
 * BlogContents seed.
 */
class BlogContentsSeed extends BcSeed
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
                'description' => '<p>このコンテンツはブログ機能により作られており、この文章については管理画面の [NEWS] &rarr; [設定] より更新ができます。また、ブログは [コンテンツ管理] よりいくつでも作成することができます。</p>',
                'template' => 'default',
                'list_count' => 10,
                'list_direction' => 'DESC',
                'feed_count' => 10,
                'tag_use' => true,
                'comment_use' => true,
                'comment_approve' => false,
                'auth_captcha' => false,
                'widget_area' => 2,
                'eye_catch_size' => 'YTo0OntzOjExOiJ0aHVtYl93aWR0aCI7czozOiIzMDAiO3M6MTI6InRodW1iX2hlaWdodCI7czozOiIzMDAiO3M6MTg6Im1vYmlsZV90aHVtYl93aWR0aCI7czozOiIxMDAiO3M6MTk6Im1vYmlsZV90aHVtYl9oZWlnaHQiO3M6MzoiMTAwIjt9',
                'use_content' => true,
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ],
            [
                'id' => 2,
                'description' => NULL,
                'template' => 'default',
                'list_count' => 10,
                'list_direction' => 'DESC',
                'feed_count' => 10,
                'tag_use' => false,
                'comment_use' => true,
                'comment_approve' => false,
                'auth_captcha' => false,
                'widget_area' => NULL,
                'eye_catch_size' => 'YTo0OntzOjExOiJ0aHVtYl93aWR0aCI7aTo2MDA7czoxMjoidGh1bWJfaGVpZ2h0IjtpOjYwMDtzOjE4OiJtb2JpbGVfdGh1bWJfd2lkdGgiO2k6MTUwO3M6MTk6Im1vYmlsZV90aHVtYl9oZWlnaHQiO2k6MTUwO30=',
                'use_content' => true,
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ],
        ];

        $table = $this->table('blog_contents');
        $table->insert($data)->save();
    }
}
