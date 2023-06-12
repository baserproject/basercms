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

use BcBlog\Test\Factory\BlogTagFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * BlogTagsScenario
 *
 * ブログコメントを生成する
 *
 */
class BlogTagsScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        BlogTagFactory::make([[
            'id' => 1,
            'name' => 'tag1',
            'created' => '2022-08-10 18:57:47',
            'modified' => NULL,
        ]])->persist();
        BlogTagFactory::make([[
            'id' => 2,
            'name' => 'tag2',
            'created' => '2022-08-10 18:57:47',
            'modified' => NULL,
        ]])->persist();
        BlogTagFactory::make([[
            'id' => 3,
            'name' => 'tag3',
            'created' => '2022-08-10 18:57:47',
            'modified' => NULL,
        ]])->persist();
    }

}
