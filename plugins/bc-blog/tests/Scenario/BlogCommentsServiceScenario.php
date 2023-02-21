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

use BcBlog\Test\Factory\BlogPostFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BlogCommentsServiceScenario
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BcBlog.Factory/BlogComments
 * - plugin.BcBlog.Factory/BlogPosts
 */
class BlogCommentsServiceScenario implements FixtureScenarioInterface
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * load
     */
    public function load(...$args)
    {
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1])->persist();
        $this->loadFixtureScenario(BlogCommentsScenario::class);
    }

}
