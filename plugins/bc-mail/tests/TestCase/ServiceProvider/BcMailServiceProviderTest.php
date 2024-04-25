<?php

namespace BcMail\Test\TestCase\ServiceProvider;

use BaserCore\TestSuite\BcTestCase;
use BcMail\Service\Admin\MailContentsAdminServiceInterface;
use BcMail\Service\Admin\MailFieldsAdminServiceInterface;
use BcMail\Service\Admin\MailMessagesAdminServiceInterface;
use BcMail\Service\Front\MailFrontServiceInterface;
use BcMail\Service\MailConfigsServiceInterface;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailFieldsServiceInterface;
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\ServiceProvider\BcMailServiceProvider;
use Cake\Core\Container;

class BcMailServiceProviderTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcMailServiceProvider = new BcMailServiceProvider();
    }

    public function tearDown(): void
    {
        unset($this->BcMailServiceProvider);
        parent::tearDown();
    }

    /**
     * test services
     */
    public function testServices()
    {
        $container = new Container();
        $this->BcMailServiceProvider->services($container);
        $this->assertTrue($container->has(MailConfigsServiceInterface::class));
        $this->assertTrue($container->has(MailContentsServiceInterface::class));
        $this->assertTrue($container->has(MailContentsAdminServiceInterface::class));
        $this->assertTrue($container->has(MailFieldsServiceInterface::class));
        $this->assertTrue($container->has(MailFieldsAdminServiceInterface::class));
        $this->assertTrue($container->has(MailMessagesServiceInterface::class));
        $this->assertTrue($container->has(MailMessagesAdminServiceInterface::class));
        $this->assertTrue($container->has(MailFrontServiceInterface::class));
    }
}