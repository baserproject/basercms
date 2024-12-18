<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

namespace BcInstaller\Test\TestCase\Mailer\Admin;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;
use BcInstaller\Mailer\Admin\InstallerMailer;
use Cake\TestSuite\EmailTrait;

/**
 * Class InstallerMailerTest
 *
 * @property  InstallerMailer $InstallerMailer
 */
class InstallerMailerTest extends BcTestCase
{

    use EmailTrait;

    /**
     * setup
     */
    public function setUp(): void
    {
        parent::setUp();
        SiteConfigFactory::make(['name' => 'email', 'value' => 'basertest@example.com'])->persist();
        SiteFactory::make(['id' => 1, 'display_name' => 'main site'])->persist();

        $this->InstallerMailer = new InstallerMailer();
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
     * beforeFilter
     */
    public function testInstalled()
    {
        $this->InstallerMailer->installed('test@example.com');

        //戻り値確認
        $this->assertEquals(['test@example.com' => 'test@example.com'], $this->InstallerMailer->getTo());
        $vars = $this->InstallerMailer->viewBuilder()->getVars();
        $this->assertEquals('test@example.com', $vars['email']);
        $this->assertEquals('https://localhost/', $vars['siteUrl']);
        $this->assertEquals('https://localhost/baser/admin/baser-core/users/login', $vars['adminUrl']);
    }

}
