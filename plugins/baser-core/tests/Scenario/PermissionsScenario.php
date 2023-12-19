<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\Scenario;

use BaserCore\Test\Factory\PermissionFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * Contents
 *
 */
class PermissionsScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args): mixed
    {
        PermissionFactory::make([
            'id' => 1,
            'no' => 1,
            'sort' => 1,
            'name' => 'システム管理',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/*',
            'auth' => 0,
            'status' => 1,
            'created' => '2015-09-30 01:21:40',
            'method' => 'ALL',
            'modified' => null,
        ])->persist();
        PermissionFactory::make([
            'id' => 2,
            'no' => 2,
            'sort' => 2,
            'name' => 'よく使う項目',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/favorites/*',
            'auth' => 1,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2015-09-30 01:21:40',
            'modified' => null,
        ])->persist();
        PermissionFactory::make([
            'id' => 3,
            'no' => 3,
            'sort' => 3,
            'name' => 'ページ管理',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/pages/*',
            'auth' => 1,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2015-09-30 01:21:40',
            'modified' => null,
        ])->persist();
        PermissionFactory::make([
            'id' => 4,
            'no' => 4,
            'sort' => 4,
            'name' => 'ページテンプレート読込・書出',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/pages/*_page_files',
            'auth' => 0,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2015-09-30 01:21:40',
            'modified' => null,
        ])->persist();
        PermissionFactory::make([
            'id' => 7,
            'no' => 7,
            'sort' => 7,
            'name' => '新着情報記事管理',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/blog/blog_posts/*',
            'auth' => 1,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2015-09-30 01:21:40',
            'modified' => '2016-08-16 19:29:56',
        ])->persist();
        PermissionFactory::make([
            'id' => 9,
            'no' => 9,
            'sort' => 9,
            'name' => '新着情報カテゴリ管理',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/blog/blog_categories/*',
            'auth' => 1,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2015-09-30 01:21:40',
            'modified' => '2016-08-16 19:30:12',
        ])->persist();
        PermissionFactory::make([
            'id' => 10,
            'no' => 10,
            'sort' => 10,
            'name' => '新着情報コメント一覧',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/blog/blog_comments/*',
            'auth' => 1,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2015-09-30 01:21:40',
            'modified' => '2016-08-16 19:30:19',
        ])->persist();
        PermissionFactory::make([
            'id' => 11,
            'no' => 11,
            'sort' => 11,
            'name' => 'ブログタグ管理',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/blog/blog_tags/*',
            'auth' => 1,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2015-09-30 01:21:40',
            'modified' => null,
        ])->persist();
        PermissionFactory::make([
            'id' => 13,
            'no' => 13,
            'sort' => 13,
            'name' => 'お問い合わせ管理',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/mail/mail_fields/*',
            'auth' => 1,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2015-09-30 01:21:40',
            'modified' => '2016-08-16 19:30:34',
        ])->persist();
        PermissionFactory::make([
            'id' => 14,
            'no' => 14,
            'sort' => 14,
            'name' => 'お問い合わせ受信メール一覧',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/mail/mail_messages/*',
            'auth' => 1,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2015-09-30 01:21:40',
            'modified' => '2016-08-16 19:29:11',
        ])->persist();
        PermissionFactory::make([
            'id' => 15,
            'no' => 15,
            'sort' => 15,
            'name' => 'エディタテンプレート呼出',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/editor_templates/js',
            'auth' => 1,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2015-09-30 01:21:40',
            'modified' => null,
        ])->persist();
        PermissionFactory::make([
            'id' => 16,
            'no' => 16,
            'sort' => 16,
            'name' => 'アップローダー',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/uploader/*',
            'auth' => 1,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2015-09-30 01:21:40',
            'modified' => null,
        ])->persist();
        PermissionFactory::make([
            'id' => 17,
            'no' => 17,
            'sort' => 17,
            'name' => 'コンテンツ管理',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/baser-core/contents/*',
            'auth' => 1,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2016-08-16 19:28:39',
            'modified' => '2016-08-16 19:28:39',
        ])->persist();
        PermissionFactory::make([
            'id' => 18,
            'no' => 18,
            'sort' => 18,
            'name' => 'リンク管理',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/content_links/*',
            'auth' => 1,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2016-08-16 19:28:56',
            'modified' => '2016-08-16 19:28:56',
        ])->persist();
        PermissionFactory::make([
            'id' => 19,
            'no' => 19,
            'sort' => 19,
            'name' => 'DebugKit 管理',
            'user_group_id' => 2,
            'permission_group_id' => 1,
            'url' => '/baser/admin/debug_kit/*',
            'auth' => 1,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2021-05-06 15:25:59',
            'modified' => '2021-05-06 15:25:59',
        ])->persist();
        PermissionFactory::make([
            'id' => 20,
            'no' => 20,
            'sort' => 20,
            'name' => 'システム管理(Admin)',
            'user_group_id' => 1,
            'permission_group_id' => 1,
            'url' => '/baser/admin/*',
            'auth' => 1,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2021-07-07 15:25:59',
            'modified' => '2021-07-07 15:25:59',
        ])->persist();
        PermissionFactory::make([
            'id' => 22,
            'no' => 22,
            'sort' => 22,
            'name' => 'テストグループ3編集1',
            'user_group_id' => 3,
            'permission_group_id' => 1,
            'url' => '/baser/admin/bc-blog/blog_posts/edit/*',
            'auth' => 1,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2021-07-07 15:25:59',
            'modified' => '2021-07-07 15:25:59',
        ])->persist();
        PermissionFactory::make([
            'id' => 23,
            'no' => 23,
            'sort' => 23,
            'name' => 'テストグループ3編集2',
            'user_group_id' => 3,
            'permission_group_id' => 1,
            'url' => '/baser/admin/bc-blog/blog_posts/add',
            'auth' => 0,
            'status' => 1,
            'method' => 'ALL',
            'created' => '2021-07-07 15:25:59',
            'modified' => '2021-07-07 15:25:59',
        ])->persist();
        return null;
    }

}
