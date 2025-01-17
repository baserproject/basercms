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

namespace BaserCore\Test\TestCase\Mailer\Admin;

use BaserCore\Mailer\Admin\BcAdminMailer;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class BcAdminMailerTest
 */
class BcAdminMailerTest extends BcTestCase
{

    /**
     * @var BcAdminMailer
     */
    public $BcAdminMailer;

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
        $this->BcAdminMailer = new BcAdminMailer();
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
        $this->assertEquals('test theme', $this->BcAdminMailer->viewBuilder()->getTheme());
    }

}
