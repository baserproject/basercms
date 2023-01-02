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
 * BlogPostFactory
 */
class BlogPostFactory extends CakephpBaseFactory
{

    /**
     * Defines the Table Registry used to generate entities with
     *
     * @return string
     */
    protected function getRootTableRegistryName(): string
    {
        return 'BcBlog.BlogPosts';
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
                'no' => $faker->randomNumber(),
                'title' => $faker->title(),
                'content' => $faker->randomHtml(),
                'detail' => $faker->randomHtml(),
                'blog_category_id' => $faker->randomNumber(),
                'user_id' => $faker->randomNumber(),
                'posted' => $faker->date(),
                'content_draft' => $faker->randomHtml(),
                'detail_draft' => $faker->randomHtml(),
                'publish_begin' => null,
                'publish_end' => null,
                'exclude_search' => $faker->randomElement([true, false]),
                'eye_catch_size' => 'YTo0OntzOjExOiJ0aHVtYl93aWR0aCI7czozOiIzMDAiO3M6MTI6InRodW1iX2hlaWdodCI7czozOiIzMDAiO3M6MTg6Im1vYmlsZV90aHVtYl93aWR0aCI7czozOiIxMDAiO3M6MTk6Im1vYmlsZV90aHVtYl9oZWlnaHQiO3M6MzoiMTAwIjt9',
                'created' => $faker->date(),
                'modified' => $faker->date()
            ];
        });
    }

    public function unpublish($id, $blogContentId)
    {
        return $this->setField('id', $id)
            ->setField('blog_content_id', $blogContentId)
            ->setField('status', false);
    }

    public function publish($id, $blogContentId)
    {
        return $this->setField('id', $id)
            ->setField('blog_content_id', $blogContentId)
            ->setField('status', true);
    }
}
