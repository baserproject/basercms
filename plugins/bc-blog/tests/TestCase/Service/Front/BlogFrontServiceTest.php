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

namespace BcBlog\Test\TestCase\Service\Front;

use BaserCore\Controller\ContentFoldersController;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcBlog\Service\BlogContentsService;
use BcBlog\Service\Front\BlogFrontService;
use BcBlog\Test\Factory\BlogContentFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BlogFrontServiceTest
 * @property BlogFrontService $BlogFrontService
 */
class BlogFrontServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use ScenarioAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/Contents',
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
        $this->BlogFrontService = new BlogFrontService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogFrontService);
        parent::tearDown();
    }


    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test setupPreviewForIndex
     */
    public function test_setupPreviewForIndex()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        BlogContentFactory::make([
            'id' => 1,
            'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。',
            'template' => 'default',
            'list_count' => '10',
            'list_direction' => 'DESC',
            'feed_count' => '10',
            'tag_use' => '1',
            'comment_use' => '1',
            'comment_approve' => '0',
            'auth_captcha' => '1',
            'widget_area' => '2',
            'eye_catch_size' => BcUtil::serialize([
                'thumb_width' => 600,
                'thumb_height' => 600,
                'mobile_thumb_width' => 150,
                'mobile_thumb_height' => 150,
            ]),
            'use_content' => '1'
        ])->persist();
        ContentFactory::make([
            'id' => 1,
            'title' => 'news',
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'entity_id' => 1,
            'url' => '/test',
            'site_id' => 1,
            'alias_id' => null,
            'main_site_content_id' => null,
            'parent_id' => null,
            'lft' => 1,
            'rght' => 2,
            'level' => 1,

        ])->persist();
        $blogContent = [
            'description' => 'test',
            'template' => 'default-2',
            'content' => [
                'title' => 'preview title',
                'url' => '/preview',
            ]
        ];
        $controller = new ContentFoldersController(
            $this->getRequest()
                ->withParam('entityId', 1)
                ->withParsedBody($blogContent)
        );

        $this->BlogFrontService->setupPreviewForIndex($controller);
        $this->assertEquals('Blog/default-2/index', $controller->viewBuilder()->getTemplate());

        $vars = $controller->viewBuilder()->getVars();
        $this->assertArrayHasKey('blogContent', $vars);
        $this->assertArrayHasKey('posts', $vars);
        $this->assertArrayHasKey('single', $vars);
        $this->assertArrayNotHasKey('editLink', $vars);
        $this->assertEquals('test', $vars['blogContent']->description);
        $this->assertEquals('default-2', $vars['blogContent']->template);
        $this->assertEquals('/preview', $vars['blogContent']->content->url);
        $this->assertEquals('preview title', $vars['blogContent']->content->title);
    }

    /**
     * test getIndexTemplate
     */
    public function test_getIndexTemplate()
    {
        BlogContentFactory::make([
            'id' => 1,
            'template' => 'template-1'
        ])->persist();
        $BlogContentsService = new BlogContentsService();
        $rs = $this->BlogFrontService->getIndexTemplate($BlogContentsService->get(1));
        $this->assertEquals($rs, 'Blog/template-1/index');
    }
}
