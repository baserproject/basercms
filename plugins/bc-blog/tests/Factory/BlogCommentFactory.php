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
 * BlogCommentFactory
 */
class BlogCommentFactory extends CakephpBaseFactory
{

    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BcBlog.BlogComments';
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
                'blog_content_id' => $faker->randomNumber(),
                'blog_post_id' => $faker->randomNumber(),
                'no' => $faker->unique()->randomNumber(),
                'status' => true,
                'name' => $faker->name(),
                'email' => $faker->email(),
                'url' => $faker->url(),
                'message' => $faker->text(10),
                'created' => $faker->date(),
                'modified' => $faker->date()
            ];
        });
    }

}
