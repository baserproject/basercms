<?php

namespace BcBlog\Test\Scenario;

use BaserCore\Test\Factory\ContentFactory;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

class BlogPostsScenario implements FixtureScenarioInterface
{
    public function load(...$args): mixed
    {
        BlogContentFactory::make([
            'id' => 1,
            'description' => 'test',
            'template' => 'default',
        ])->persist();
        ContentFactory::make([
            'id' => 1,
            'title' => 'news',
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'entity_id' => 1,
            'url' => '/test',
            'exclude_search' => null,
            'status' => true,
        ])->persist();
        BlogContentFactory::make([
            'id' => 2,
            'description' => 'test',
            'template' => 'default',
        ])->persist();
        ContentFactory::make([
            'id' => 2,
            'title' => 'news',
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'entity_id' => 2,
            'url' => '/test',
            'exclude_search' => 1,
            'status' => true,
        ])->persist();
        BlogPostFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'no' => 3,
            'name' => 'release',
            'title' => 'プレスリリース',
            'status' => 1,
            'posted' => '2015-01-27 12:57:59',
        ])->persist();
        BlogPostFactory::make([
            'id' => 2,
            'blog_content_id' => 2,
            'no' => 4,
            'name' => 'smartphone_release',
            'title' => 'スマホサイトリリース',
            'status' => 1,
            'posted' => '2016-02-10 12:57:59',
        ])->persist();
        return null;
    }
}