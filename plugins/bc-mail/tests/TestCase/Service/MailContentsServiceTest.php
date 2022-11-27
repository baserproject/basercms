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

namespace BcMail\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BcMail\Service\MailContentsService;
use BcMail\Service\MailContentsServiceInterface;

/**
 * MailContentsServiceTest
 *
 * @property MailContentsService $MailContentsService
 */
class MailContentsServiceTest extends BcTestCase
{

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->MailContentsService = $this->getService(MailContentsServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->MailContentsService);
    }

    /**
     * test constructor
     */
    public function test__construct()
    {
        $this->assertEquals('mail_contents', $this->MailContentsService->MailContents->getTable());
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test create
     */
    public function test_create()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
