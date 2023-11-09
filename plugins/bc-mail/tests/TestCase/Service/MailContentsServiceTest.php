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

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMail\Service\MailContentsService;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Test\Factory\MailContentFactory;
use BcMail\Test\Scenario\MailContentsScenario;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Error;

/**
 * MailContentsServiceTest
 *
 * @property MailContentsService $MailContentsService
 */
class MailContentsServiceTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

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
     * 一覧データ取得
     */
    public function test_getIndex()
    {
        //データを生成
        $this->loadFixtureScenario(MailContentsScenario::class);
        //一覧データ取得サービスをコル
        $rs = $this->MailContentsService->getIndex([])->toArray();
        //戻る値を確認
        $this->assertCount(1, $rs);
        $this->assertEquals('description test', $rs[0]->description);
        $this->assertEquals('お問い合わせ', $rs[0]->content->title);
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $result = $this->MailContentsService->getNew();
        $this->assertEquals('お問い合わせ頂きありがとうございます', $result->subject_user);
        $this->assertEquals('お問い合わせを頂きました', $result->subject_admin);
        $this->assertEquals('default', $result->layout_template);
        $this->assertEquals('default', $result->form_template);
        $this->assertEquals('mail_default', $result->mail_template);
        $this->assertEquals(true, $result->use_description);
        $this->assertEquals(true, $result->save_info);
        $this->assertEquals(false, $result->validate);
        $this->assertEquals(false, $result->ssl_on);
    }

    /**
     * test get
     */
    public function test_get()
    {
        $options = [
            'contain' => []
        ];
        $this->loadFixtureScenario(MailContentsScenario::class);
        $result = $this->MailContentsService->get(1, $options)->toArray();
        $this->assertEquals('description test', $result['description']);

        $options = [
            'contain' => [
                'Contents' => ['Sites'],
                'MailFields'
            ]
        ];
        $result = $this->MailContentsService->get(1, $options)->toArray();
        $this->assertEquals('description test', $result['description']);
        $this->assertEquals('お問い合わせ', $result['content']['title']);

    }

    /**
     * test create
     */
    public function test_create()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $data = [
            'content' => [
                'name' => 'コンテンツ名',
                'title' => 'add mail content',
                'site_id' => 1,
                'parent_id' => 0
            ],
            'description' => 'Nghiem',
        ];
        $mailContent = $this->MailContentsService->create($data, []);
        $this->assertEquals('Nghiem', $mailContent->description);
        $data = [
            'description' => 'Nghiem',
        ];
        $this->expectException(PersistenceFailedException::class);
        $this->MailContentsService->create($data, []);
    }

    /**
     * リストデータ取得
     */
    public function test_getList()
    {
        //データを生成
        $this->loadFixtureScenario(MailContentsScenario::class);
        //一覧データ取得サービスをコル
        $rs = $this->MailContentsService->getList();
        //戻る値を確認
        $this->assertCount(1, $rs);
        $this->assertEquals('お問い合わせ', $rs[1]);
    }

    /**
     * test delete
     * @throws \Throwable
     */
    public function test_delete()
    {
        //data create
        $this->loadFixtureScenario(MailContentsScenario::class);

        $mailContent = $this->MailContentsService->get(1, ['contain' => []]);
        $this->assertEquals(1, $mailContent->id);

        $result = $this->MailContentsService->delete(1);
        $this->assertTrue($result);
        $this->expectException(RecordNotFoundException::class);
        $this->MailContentsService->get(1, ['contain' => []]);

        $result = $this->MailContentsService->delete(0);
        $this->assertFalse($result);

    }

    /**
     * update test
     */
    public function test_update()
    {
        $this->loadFixtureScenario(MailContentsScenario::class);
        $mailContent = $this->MailContentsService->get(1, ['contain' => []]);
        $data = [
            'content' => [
                'name' => 'コンテンツ名',
                'title' => 'add mail content',
                'site_id' => 1,
                'parent_id' => 0
            ],
            'description' => 'Nghiem',
        ];
        $result = $this->MailContentsService->update($mailContent, $data);
        $this->assertEquals('Nghiem', $result->description);

        $data = [
            'description' => 'Nghiem',
        ];
        $this->expectException(PersistenceFailedException::class);
        $this->MailContentsService->update($mailContent, $data);
    }

    /**
     * copy test
     */
    public function test_copy()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $data = [
            'entity_id' => 1,
            'parent_id' => 1,
            'title' => 'Nghiem',
            'site_id' => 1
        ];
        $this->loginAdmin($this->getRequest());
        $result = $this->MailContentsService->copy($data);
        $this->assertEquals('Nghiem', $result->content->title);

        $data['entity_id'] = 0;
        $this->expectException(Error::class);
        $this->MailContentsService->copy($data);
    }

    /**
     * copy getPublishedAll
     */
    public function test_getPublishedAll()
    {
        //準備
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        //正常系実行
        $result = $this->MailContentsService->getPublishedAll(1);
        $this->assertCount(1, $result);
        //レコードを追加した後、返するレコード数を確認する
        MailContentFactory::make([
            'id' => 3,
            'description' => 'description test',
            'ssl_on' => 0,
            'save_info' => 1,
        ])->persist();
        ContentFactory::make([
            'id' => 10,
            'name' => 'name_test',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'url' => '/contact/',
            'site_id' => 1,
            'title' => 'お問い合わせ',
            'entity_id' => 3,
            'parent_id' => 1,
            'rght' => 1,
            'lft' => 2,
            'status' => true,
        ])->persist();
        $result = $this->MailContentsService->getPublishedAll(1);
        $this->assertCount(2, $result);
        //異常系実行
        $result = $this->MailContentsService->getPublishedAll(99);
        $this->assertCount(0, $result);
    }

}
