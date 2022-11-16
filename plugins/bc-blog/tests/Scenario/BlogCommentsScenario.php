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

use BcBlog\Test\Factory\BlogCommentFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * BlogContentScenario
 *
 * ブログコメントを生成する
 * BlogComment を生成する
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BcBlog.Factory/BlogComments
 */
class BlogCommentsScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        BlogCommentFactory::make([[
            'id' => 1,
            'blog_content_id' => 1,
            'blog_post_id' => 1,
            'no' => 1,
            'status' => 1,
            'name' => 'baserCMS',
            'email' => '',
            'url' => 'https://basercms.net',
            'message' => 'ホームページの開設おめでとうございます。（ダミー）',
            'created' => '2015-08-10 18:57:47',
            'modified' => NULL,
        ]])->persist();
        BlogCommentFactory::make([[
            'id' => 2,
            'blog_content_id' => 1,
            'blog_post_id' => 1,
            'no' => 2,
            'status' => 1,
            'name' => 'baserCMS',
            'email' => '',
            'url' => 'https://basercms.net',
            'message' => 'Comment no 2',
            'created' => '2015-08-10 18:57:47',
            'modified' => NULL,
        ]])->persist();
        BlogCommentFactory::make([[
            'id' => 3,
            'blog_content_id' => 1,
            'blog_post_id' => 1,
            'no' => 3,
            'status' => false,
            'name' => 'baserCMS',
            'email' => '',
            'url' => 'https://basercms.net',
            'message' => 'Comment no 3',
            'created' => '2015-08-10 18:57:47',
            'modified' => NULL,
        ]])->persist();
    }

}
