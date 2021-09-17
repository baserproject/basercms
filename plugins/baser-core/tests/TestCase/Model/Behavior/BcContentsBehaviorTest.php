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
namespace BaserCore\Test\TestCase\Model\Behavior;

use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Table\ContentFoldersTable;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\Behavior\BcContentsBehavior;

/**
 * Class BcContentsBehaviorTest
 * @package BaserCore\Test\TestCase\Model\Behavior
 * @property ContentFoldersTable $ContentsFolder
 *
 */
class BcContentsBehaviorTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
    ];

    /**
     * @var ContentFoldersTable|BcContentsBehavior;
     */
    protected $table;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->table = $this->getTableLocator()->get('BaserCore.ContentFolders');
        $this->table->setPrimaryKey(['id']);
        $this->table->addBehavior('BaserCore.BcContents');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertTrue($this->table->__isset('Contents'));
    }
    /**
     * Setup
     */
    public function testSetup()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Before validate
     *
     * Content のバリデーションを実行
     * 本体のバリデーションも同時に実行する為、Contentのバリデーション判定は、 beforeSave にて確認
     */
    public function testBeforeValidate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Before save
     *
     * Content のバリデーション結果確認
     */
    public function testBeforeSave()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * After save
     *
     * Content を保存する
     */
    public function testAfterSave()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Before delete
     *
     * 削除した Content ID を一旦保管し、afterDelete で Content より削除する
     */
    public function testBeforeDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * After delete
     *
     * 削除したデータに連携する Content を削除
     */
    public function testAfterDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 公開されたコンテンツを取得する
     */
    public function testFindPublished()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
