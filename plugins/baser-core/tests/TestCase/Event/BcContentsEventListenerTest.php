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

namespace BaserCore\Test\TestCase\Event;

use BaserCore\Model\Entity\Page;
use Cake\Event\Event;
use BaserCore\View\BcAdminAppView;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Event\BcContentsEventListener;

/**
 * Class BcContentsEventListenerTest
 *
 * @property  BcContentsEventListener $BcContentsEventListener
 */
class BcContentsEventListenerTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Sites',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcContentsEventListener = new BcContentsEventListener('page');
        $BcAdminAppView = new BcAdminAppView($this->getRequest('/baser/admin'));
        $this->BcAdminAppView = $BcAdminAppView->setPlugin("BcAdminThird");
        $this->Content = $this->getTableLocator()->get('Contents')->get(1);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcContentsEventListener, $this->BcAdminAppView, $this->Content);
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test__construct(){
        $this->assertNotEmpty($this->BcContentsEventListener);
    }
    /**
     * Implemented Events
     */
    public function testImplementedEvents()
    {
        $this->assertTrue(is_array($this->BcContentsEventListener->implementedEvents()));
    }

    /**
     * Form Before Create
     */
    public function testFormBeforeCreate()
    {
        $event = new Event("Helper.Form.beforeCreate", $this->BcAdminAppView);
        $this->BcContentsEventListener->formBeforeCreate($event);
        $this->assertEquals(['type' => 'file'], $event->getData('options'));
    }

    /**
     * Form After Create
     */
    public function testFormAfterCreate()
    {
        // 正常系
        $event = new Event("Helper.Form.afterCreate", $this->BcAdminAppView->set('content', $this->Content)); // content_fieldsの$contentが足りないため追加
        $event->setData('id', 'TestAdminEditForm')->setData('out', "testtest");
        // NOTE: 必要な要素があるかを判別するため、不要なエラーを制御
        $result = @$this->BcContentsEventListener->formAfterCreate($event);
        // outの文章が含まれているかチェック
        $this->assertStringContainsString("testtest", $result);
        // content_fieldsの文章が含まれているかチェック
        $this->assertStringContainsString("公開URLを開きます", $result);
    }

    /**
     * Form After Submit
     *
     * フォームの保存ボタンの前後に、一覧、プレビュー、削除ボタンを配置する
     * プレビューを配置する場合は、設定にて、preview を true にする
     */
    public function testFormAfterSubmit()
    {
        // 正常系
        $request = $this->getRequest('/baser/admin')->withData('ContentFolder.content', $this->Content)
            ->withParam('action', 'edit'); // content_infoで必要
        $this->loginAdmin($request);
        $BcAdminAppView = $this->BcAdminAppView->setRequest($request)->setPlugin("BcAdminThird")
            ->set("entityName", "Contents.")
            ->set("relatedContents", [
                ['Site' => [
                    'id' => '1',
                    'name' => 'test',
                    'main_site_id' => ''
                ]],
                ['Site' => [
                    'id' => '2',
                    'name' => 'test1',
                    'main_site_id' => '1'
                ]]
            ])
            ->set("content", $this->Content); // content_relatedで必要
        $out = "testtest";
        $entity = new Page(['content' => []]);
        $BcAdminAppView->BcAdminForm->create($entity);
        $event = new Event("Helper.Form.afterSubmit", $BcAdminAppView);
        $event->setData('id', 'TestAdminEditForm')->setData('out', $out);
        @$this->BcContentsEventListener->formAfterSubmit($event); // NOTE: 必要な要素があるかを判別するため、不要なエラーを制御
        $result = $event->getData('out');
        $checkList = [
            $out, // outの文章が含まれているかチェック
            "説明文", // content_optionsの文章が含まれているかチェック
            "一覧に戻る", // content_actionsの文章が含まれているかチェック
            "関連コンテンツ", // content_relatedの文章が含まれているかチェック
            "その他情報" // content_infoの文章が含まれているかチェック
        ];
        foreach ($checkList as $text) {
            $this->assertStringContainsString($text, $result);
        }
        // 異常系 isAdminSystem()がfalseの場合 または、イベント登録されたidがマッチしない場合
        $event = new Event("Helper.Form.afterSubmit", $this->BcAdminAppView->setRequest($this->getRequest()));
        $event->setData('out', $out);
        $this->BcContentsEventListener->formAfterSubmit($event);
        $result = $event->getData('out');
        $this->assertEquals($out, $result);
    }
}
