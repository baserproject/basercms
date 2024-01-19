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

namespace BcBlog\Test\Scenario;

use BaserCore\Test\Factory\ContentFactory;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * BlogPostsAdminServiceScenario
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * 'plugin.BcBlog.Factory/BlogPosts',
 * 'plugin.BcBlog.Factory/BlogContents',
 * 'plugin.BaserCore.Factory/Contents',
 */
class BlogPostsAdminServiceScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args): mixed
    {
        ContentFactory::make([
            'id' => 100,
            'url' => '/index',
            'site_id' => 1,
            'status' => true,
            'entity_id' => 1,
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'lft' => '1',
            'rght' => '2',
            'publish_begin' => '2020-01-27 12:00:00',
            'publish_end' => '9000-01-27 12:00:00'
        ])->persist();
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1, 'no' => 1, 'status' => true])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        return null;
    }

}
