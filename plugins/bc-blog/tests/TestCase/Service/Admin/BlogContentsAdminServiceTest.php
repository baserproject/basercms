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
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Service\Admin\BlogContentsAdminService;
use BcBlog\Service\BlogContentsService;
use BcBlog\Test\Factory\BlogContentFactory;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BlogContentsAdminServiceTest
 * @property BlogContentsAdminService $BlogContentsAdminService
 */
class BlogContentsAdminServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use IntegrationTestTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogContentsAdminService = new BlogContentsAdminService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogContentsAdminService);
        parent::tearDown();
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        BlogContentFactory::make([
            'id' => '1',
            'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。',
            'use_content' => '1',
        ])->persist();
        ContentFactory::make(
            [
                'id' => 1,
                'plugin' => 'BcBlog',
                'type' => 'BlogContent',
                'entity_id' => 1,
                'url' => '/news/',
                'site_id' => 1,
                'lft' => 1,
                'rght' => 2,
                'status' => true,
            ]
        )->persist();
        SiteFactory::make(['id' => 1, 'use_subdomain' => 0])->persist();
        $blogContentService = new BlogContentsService();
        $blogContent = $blogContentService->get(1);

        $rs = $this->BlogContentsAdminService->getViewVarsForEdit($blogContent);
        $this->assertEquals($blogContent, $rs['blogContent']);
        $this->assertEquals('https://localhost/news/', $rs['publishLink']);
        $this->assertArrayHasKey('editorEnterBr', $rs);
    }
}
