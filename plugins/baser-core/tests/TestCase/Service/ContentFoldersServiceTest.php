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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Service\ContentFoldersService;

/**
 * BaserCore\Model\Table\ContentFoldersTable Test Case
 *
 * @property ContentFoldersService $ContentFoldersService
 */
class ContentFoldersServiceTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var ContentFoldersService
     */
    public $ContentFolders;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
    ];

        /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentFoldersService = new ContentFoldersService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentFoldersService);
        parent::tearDown();
    }

    /**
     * Test create
     */
    public function testCreate()
    {
        $data = [
            'folder_template' => 'テストcreate',
        ];
        $result = $this->ContentFoldersService->create($data);
        $expected = $this->ContentFoldersService->ContentFolders->find()->last();
        $this->assertEquals($expected->name, $result->name);
    }
}
