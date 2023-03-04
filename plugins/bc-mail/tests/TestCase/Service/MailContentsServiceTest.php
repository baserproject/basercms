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
use BcMail\Test\Scenario\MailContentsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

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
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Contents',
        'plugin.BcMail.Factory/MailContents',
    ];

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
        $this->assertCount(2, $rs);
        $this->assertEquals($rs[1]->description, 'description test');
        $this->assertEquals($rs[1]->content->title, 'お問い合わせ');
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
        $this->assertCount(2, $rs);
        $this->assertEquals('お問い合わせ',$rs[1]);
        $this->assertEquals('テスト',$rs[2]);
    }

}
