<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Event;

use Cake\Event\Event;
use BaserCore\View\BcAdminAppView;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Event\BcContentsEventListener;

/**
 * Class BcContentsEventListenerTest
 *
 * @package BaserCore\Test\TestCase\Event
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
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcContentsEventListener = new BcContentsEventListener();
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
        $result = @$this->BcContentsEventListener->formAfterCreate($event); // NOTE: 必要な要素があるかを判別するため、不要なエラーを制御
        // outの文章が含まれているかチェック
        $this->assertStringContainsString("testtest", $result);
        // content_fieldsの文章が含まれているかチェック
        $this->assertStringContainsString("親フォルダの設定を継承し公開期間が設定されている状態となっています", $result);
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
        $BcAdminAppView = $this->BcAdminAppView->setRequest($request)->setPlugin("BcAdminThird")
            ->set("relatedContents", ['test1', 'test2']); // content_relatedで必要
        $out = "testtest";
        $event = new Event("Helper.Form.afterSubmit", $BcAdminAppView);
        $event->setData('id', 'TestAdminEditForm')->setData('out', "testtest");
        $result = @$this->BcContentsEventListener->formAfterSubmit($event); // NOTE: 必要な要素があるかを判別するため、不要なエラーを制御
        // outの文章が含まれているかチェック
        $this->assertStringContainsString("testtest", $result);
        // content_optionsの文章が含まれているかチェック
        $this->assertStringContainsString("メニューのリンクを別ウィンドウ開く", $result);
        // content_actionsの文章が含まれているかチェック
        $this->assertStringContainsString("一覧に戻る", $result);
        // content_relatedの文章が含まれているかチェック
        $this->assertStringContainsString("関連コンテンツ", $result);
        // content_infoの文章が含まれているかチェック
        $this->assertStringContainsString("その他情報", $result);
        // 異常系 isAdminSystem()がfalseの場合 または、イベント登録されたidがマッチしない場合
        $event = new Event("Helper.Form.afterSubmit", $this->BcAdminAppView->setRequest($this->getRequest()));
        $event->setData('out', $out);
        $result = $this->BcContentsEventListener->formAfterSubmit($event);
        $this->assertEquals($out, $result);
    }
}
