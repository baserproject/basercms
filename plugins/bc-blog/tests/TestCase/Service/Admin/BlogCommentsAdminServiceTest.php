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

namespace BcBlog\Test\TestCase\Service\Admin;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Service\Admin\BlogCommentsAdminService;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Scenario\BlogCommentsServiceScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BlogCommentsAdminServiceTest
 * @property BlogCommentsAdminService $BlogCommentsAdminService
 */
class BlogCommentsAdminServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BcBlog.Factory/BlogPosts',
        'plugin.BcBlog.Factory/BlogComments',
        'plugin.BcBlog.Factory/BlogContents',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->BlogCommentsAdminService = new BlogCommentsAdminService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogCommentsAdminService);
        parent::tearDown();
    }

    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        //データ生成
        $this->loadFixtureScenario(BlogCommentsServiceScenario::class);
        ContentFactory::make(['entity_id' => 1, 'plugin' => 'BcBlog', 'type' => 'BlogContent'])->persist();
        BlogContentFactory::make(['id' => 1, 'description' => 'test view'])->persist();

        //メソードをコル
        $rs = $this->BlogCommentsAdminService->getViewVarsForIndex(
            1,
            1,
            $this->BlogCommentsAdminService->getIndex([])->all()
        );

        //戻り値を確認
        $this->assertEquals($rs['blogContent']['description'], 'test view');
        $this->assertEquals($rs['blogPost']->id, 1);

        //blogComment値を確認
        $blogComment = $rs['blogComments']->first();
        $this->assertEquals(count($rs['blogComments']), 3);
        $this->assertEquals($blogComment['name'], 'baserCMS');
        $this->assertEquals($blogComment['url'], 'https://basercms.net');
    }
}
