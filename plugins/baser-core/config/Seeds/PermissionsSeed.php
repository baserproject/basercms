<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Permissions seed.
 */
class PermissionsSeed extends AbstractSeed
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
                'no' => 1,
                'sort' => 1,
                'name' => 'システム管理',
                'user_group_id' => 2,
                'url' => '/baser/admin/*',
                'auth' => 0,
                'status' => 1,
                'created' => '2022-10-01 09:00:00',
                'method' => 'ALL',
                'modified' => null,
            ],
            [
                'id' => 2,
                'no' => 2,
                'sort' => 2,
                'name' => 'ページ管理',
                'user_group_id' => 2,
                'url' => '/baser/admin/baser-core/pages/*',
                'auth' => 1,
                'status' => 1,
                'method' => 'ALL',
                'created' => '2022-10-01 09:00:00',
                'modified' => null,
            ],
            [
                'id' => 3,
                'no' => 3,
                'sort' => 3,
                'name' => '新着情報記事管理',
                'user_group_id' => 2,
                'url' => '/baser/admin/bc-blog/blog_posts/*',
                'auth' => 1,
                'status' => 1,
                'method' => 'ALL',
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ],
            [
                'id' => 4,
                'no' => 4,
                'sort' => 4,
                'name' => '新着情報カテゴリ管理',
                'user_group_id' => 2,
                'url' => '/baser/admin/bc-blog/blog_categories/*',
                'auth' => 1,
                'status' => 1,
                'method' => 'ALL',
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ],
            [
                'id' => 5,
                'no' => 5,
                'sort' => 5,
                'name' => '新着情報コメント一覧',
                'user_group_id' => 2,
                'url' => '/baser/admin/bc-blog/blog_comments/*',
                'auth' => 1,
                'status' => 1,
                'method' => 'ALL',
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ],
            [
                'id' => 6,
                'no' => 6,
                'sort' => 6,
                'name' => 'ブログタグ管理',
                'user_group_id' => 2,
                'url' => '/baser/admin/bc-blog/blog_tags/*',
                'auth' => 1,
                'status' => 1,
                'method' => 'ALL',
                'created' => '2022-10-01 09:00:00',
                'modified' => null,
            ],
            [
                'id' => 7,
                'no' => 7,
                'sort' => 7,
                'name' => 'お問い合わせ管理',
                'user_group_id' => 2,
                'url' => '/baser/admin/bc-mail/mail_fields/*',
                'auth' => 1,
                'status' => 1,
                'method' => 'ALL',
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ],
            [
                'id' => 8,
                'no' => 8,
                'sort' => 8,
                'name' => 'お問い合わせ受信メール一覧',
                'user_group_id' => 2,
                'url' => '/baser/admin/bc-mail/mail_messages/*',
                'auth' => 1,
                'status' => 1,
                'method' => 'ALL',
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ],
            [
                'id' => 9,
                'no' => 9,
                'sort' => 9,
                'name' => 'アップローダー',
                'user_group_id' => 2,
                'url' => '/baser/admin/bc-uploader/*',
                'auth' => 1,
                'status' => 1,
                'method' => 'ALL',
                'created' => '2022-10-01 09:00:00',
                'modified' => null,
            ],
            [
                'id' => 10,
                'no' => 10,
                'sort' => 10,
                'name' => 'コンテンツ管理',
                'user_group_id' => 2,
                'url' => '/baser/admin/baser-core/contents/*',
                'auth' => 1,
                'status' => 1,
                'method' => 'ALL',
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ],
            [
                'id' => 11,
                'no' => 11,
                'sort' => 11,
                'name' => 'リンク管理',
                'user_group_id' => 2,
                'url' => '/baser/admin/baser-core/content_links/*',
                'auth' => 1,
                'status' => 1,
                'method' => 'ALL',
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ]
        ];

        $table = $this->table('permissions');
        $table->insert($data)->save();
    }
}
