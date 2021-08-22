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

use BaserCore\TestSuite\BcTestCase;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * PagesController
 */
class PagesControllerTest extends BcTestCase
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
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.Plugins',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
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

    public function testDisplay(): void
    {
        $plugins = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
        $plugins->save($plugins->newEntity([
            'name' => 'BcSample',
            'title' => 'title',
            'status' => true
        ]));
        $this->get('/');
        $this->assertResponseOk();
    }

}
