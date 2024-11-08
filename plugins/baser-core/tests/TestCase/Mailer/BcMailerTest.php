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

namespace BaserCore\Test\TestCase\Mailer;

use BaserCore\Mailer\BcMailer;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;
use Cake\Routing\Router;

/**
 * Class BcMailerTest
 */
class BcMailerTest extends BcTestCase
{

    /**
     * @var BcMailer
     */
    public $BcMailer;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        SiteConfigFactory::make(['name' => 'email', 'value' => 'basertest@example.com'])->persist();
        SiteConfigFactory::make(['name' => 'admin-theme', 'value' => 'test theme'])->persist();
        SiteFactory::make(['id' => '1', 'theme' => 'BcFront', 'status' => true])->persist();
        ContentFactory::make(['id' => 1, 'plugin' => 'BcMail', 'type' => 'MailContent', 'entity_id' => 1, 'url' => '/contact/', 'site_id' => 1, 'lft' => 1, 'rght' => 2])->persist();
        Router::setRequest($this->getRequest('/contact/'));
        $this->BcMailer = new BcMailer();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        $this->assertEquals('BcFront', $this->BcMailer->viewBuilder()->getTheme());
        $this->assertEquals(['basertest@example.com' => 'basertest@example.com'], $this->BcMailer->getFrom());
    }

    /**
     * test setEmailTransport
     */
    public function testSetEmailTransport()
    {
        $this->markTestIncomplete('まだ実装されません');
    }

    /**
     * test getPlugin
     */
    public function testGetPlugint()
    {
        $this->markTestIncomplete('まだ実装されません');
    }

    /**
     * test deliver
     */
    public function testDeliver()
    {
        $this->markTestIncomplete('まだ実装されません');
    }

}
