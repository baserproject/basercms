<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\Controller\ContentFoldersController;

/**
 * ContentFoldersController
 */
class ContentFoldersControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {

        parent::setUp();
        $this->ContentFoldersController = new ContentFoldersController($this->getRequest());
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->ContentFoldersController->BcFrontContents);
    }

    /**
     * testDisplay
     *
     * @return void
     */
    public function testView(): void
    {
        $this->get("/en");
        $this->assertResponseOk();
    }

}
