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

namespace BcCustomContent\Test\TestCase\Controller;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Controller\CustomContentController;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomEntriesScenario;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use Cake\Http\ServerRequest;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomContentsControllerTest
 */
class CustomContentsControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var CustomContentController
     */
    public $CustomContentsController;

    /**
     * Test subject
     *
     * @var ServerRequest
     */
    public $request;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomContentsController = new CustomContentController($this->getRequest());
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->CustomContentsController, $this->request);
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertNotEmpty($this->CustomContentsController->BcFrontContents);
    }

    /**
     * test index
     */
    public function test_index()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'publish_begin' => '2021-10-01 00:00:00',
            'publish_end' => '9999-11-30 23:59:59',
            'has_child' => 0
        ]);
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        //対象URLをコル
        $this->get('/test/');
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertResponseCode(200);
        $this->assertEquals('サービスタイトル', $vars['title']);
        $this->assertNotNull($vars['customContent']);
        $this->assertNotNull($vars['customEntries']);

        //存在しないURLを指定した場合、
        $this->get('/test-false/');
        $this->assertResponseCode(404);
        $this->assertEquals(
            'カスタムコンテンツにカスタムテーブルが紐付けられていません。カスタムコンテンツの編集画面よりカスタムテーブルを選択してください。',
            $_SESSION['Flash']['flash'][0]['message']
        );
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test view
     */
    public function test_view()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'publish_begin' => '2021-10-01 00:00:00',
            'publish_end' => '9999-11-30 23:59:59',
            'has_child' => 0
        ]);
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        //対象URLをコル
        $this->get('/test/view/プログラマー');
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertResponseCode(200);
        $this->assertEquals('サービスタイトル', $vars['title']);
        $this->assertNotNull($vars['customContent']);
        $this->assertNotNull($vars['customEntry']);

        //存在しないURLを指定した場合、
        $this->get('/test-false/view/プログラマー');
        $this->assertResponseCode(404);
        $this->assertEquals(
            'カスタムコンテンツにカスタムテーブルが紐付けられていません。カスタムコンテンツの編集画面よりカスタムテーブルを選択してください。',
            $_SESSION['Flash']['flash'][0]['message']
        );
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }
}
