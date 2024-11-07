<?php

namespace BcMail\Test\TestCase\Model\Entity;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\TestSuite\BcTestCase;
use BcMail\Model\Entity\MailContent;
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\Test\Factory\MailContentFactory;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class MailContentTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->MailContent = new MailContent();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }


    /**
     * Test getNumberOfMessages
     */
    public function testGetNumberOfMessages()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);

        $rs = $this->MailContent->getNumberOfMessages();
        $this->assertEquals(0, $rs);

        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailContentId = 1;
        $mailMessageTable->setup($mailContentId);
        $mailMessageTable->save(new Entity(['id' => 1]));
        $mailMessageTable->save(new Entity(['id' => 2]));
        // テストデータを作成する
        ContentFactory::make([
            'id' => 9,
            'name' => 'contact',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'entity_id' => 1,
            'url' => '/contact/',
            'site_id' => 1,
            'title' => 'お問い合わせ(※関連Fixture未完了)',
            'status' => true,
        ])->persist();
        MailContentFactory::make(['id' => 1, 'save_info' => 1])->persist();

        $this->MailContent->id = $mailContentId;

        $rs = $this->MailContent->getNumberOfMessages();
        $this->assertEquals(2, $rs);

        //不要なテーブルを削除
        $MailMessagesService->dropTable(1);
    }

}
