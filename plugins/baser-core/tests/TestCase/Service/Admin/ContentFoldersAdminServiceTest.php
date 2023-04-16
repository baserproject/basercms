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

namespace BaserCore\Test\TestCase\Service\Admin;

use BaserCore\Service\Admin\ContentFoldersAdminService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class ContentFoldersAdminServiceTest
 * @property ContentFoldersAdminService $Users
 */
class ContentFoldersAdminServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
    ];

    /**
     * @var ContentFoldersAdminService|null
     */
    public $ContentFolders = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentFolders = new ContentFoldersAdminService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentFolders);
        parent::tearDown();
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        $contentFolder = $this->ContentFolders->get(1);
        $vars = $this->ContentFolders->getViewVarsForEdit($contentFolder);
        $this->assertTrue(isset($vars['folderTemplateList']));
        $this->assertTrue(isset($vars['contentFolder']));
        $this->assertTrue(isset($vars['pageTemplateList']));
    }

}
