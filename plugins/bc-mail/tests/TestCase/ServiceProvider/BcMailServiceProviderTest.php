<?php

namespace BcMail\Test\TestCase\ServiceProvider;

use BaserCore\TestSuite\BcTestCase;
use BcMail\ServiceProvider\BcMailServiceProvider;
use Cake\Core\Container;

class BcMailServiceProviderTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->Provider = new BcMailServiceProvider();
    }

    public function tearDown(): void
    {
        unset($this->Provider);
        parent::tearDown();
    }

    public function testServices()
    {
        $container = new Container();
        $this->Provider->services($container);
        $mailConfigsService = $container->get('BcMail\Service\MailConfigsServiceInterface');
        $this->assertEquals('BcMail\Service\MailConfigsService', get_class($mailConfigsService));
        $mailContentsService = $container->get('BcMail\Service\MailContentsServiceInterface');
        $this->assertEquals('BcMail\Service\MailContentsService', get_class($mailContentsService));
        $mailContentsAdminService = $container->get('BcMail\Service\Admin\MailContentsAdminServiceInterface');
        $this->assertEquals('BcMail\Service\Admin\MailContentsAdminService', get_class($mailContentsAdminService));
        $mailFieldsService = $container->get('BcMail\Service\MailFieldsServiceInterface');
        $this->assertEquals('BcMail\Service\MailFieldsService', get_class($mailFieldsService));
        $mailFieldsAdminService = $container->get('BcMail\Service\Admin\MailFieldsAdminServiceInterface');
        $this->assertEquals('BcMail\Service\Admin\MailFieldsAdminService', get_class($mailFieldsAdminService));
        $mailMessagesService = $container->get('BcMail\Service\MailMessagesServiceInterface');
        $this->assertEquals('BcMail\Service\MailMessagesService', get_class($mailMessagesService));
        $mailMessagesAdminService = $container->get('BcMail\Service\Admin\MailMessagesAdminServiceInterface');
        $this->assertEquals('BcMail\Service\Admin\MailMessagesAdminService', get_class($mailMessagesAdminService));
        $mailFrontService = $container->get('BcMail\Service\Front\MailFrontServiceInterface');
        $this->assertEquals('BcMail\Service\Front\MailFrontService', get_class($mailFrontService));
    }
}