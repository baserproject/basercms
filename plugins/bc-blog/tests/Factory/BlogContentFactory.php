<?php
declare(strict_types=1);

/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBlog\Test\Factory;

use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;

/**
 * BlogContentFactory
 */
class BlogContentFactory extends CakephpBaseFactory
{

    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BcBlog.BlogContents';
    }

    /**
     * Defines the factory's default values. This is useful for
     * not nullable fields. You may use methods of the present factory here too.
     *
     * @return void
     */
    protected function setDefaultTemplate(): void
    {
        $this->setDefaultData(function (Generator $faker) {
            return [
                'description' => $faker->text(10),
                'template' => 'default',
                'list_count' => 10,
                'list_direction' => 'DESC',
                'feed_count' => 10,
                'tag_use' => true,
                'comment_use' => true,
                'comment_approve' => false,
                'auth_captcha' => false,
                'widget_area' => $faker->randomNumber(),
                'eye_catch_size' => 'YTo0OntzOjExOiJ0aHVtYl93aWR0aCI7czozOiIzMDAiO3M6MTI6InRodW1iX2hlaWdodCI7czozOiIzMDAiO3M6MTg6Im1vYmlsZV90aHVtYl93aWR0aCI7czozOiIxMDAiO3M6MTk6Im1vYmlsZV90aHVtYl9oZWlnaHQiO3M6MzoiMTAwIjt9',
                'use_content' => true,
                'created' => $faker->date(),
                'modified' => $faker->date()
            ];
        });
    }

    /**
     * アイキャッチコピー用
     *
     * @return BlogContentFactory
     */
    public function forCopyEyeCatch()
    {
        return $this->setField('id', '999')
            ->setField('description', 'test999');
    }

}
