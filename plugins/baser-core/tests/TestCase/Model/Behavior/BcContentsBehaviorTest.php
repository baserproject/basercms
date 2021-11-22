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

use ArrayObject;
use Cake\ORM\Entity;
use Cake\ORM\Marshaller;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Service\ContentService;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Table\ContentFoldersTable;
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
        'plugin.BaserCore.Sites',
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
        $this->contentService = new ContentService();
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
    * Test beforeMarshal
    */
    public function testBeforeMarshal()
    {
        $this->loginAdmin($this->getRequest());
        // 更新の場合はreturnされる
        $data = [
            'folder_template' => 'テストBeforeSave',
            'content' => ["id" => 1]
            ];
        $return = $this->table->dispatchEvent('Model.beforeMarshal', ['data' => new ArrayObject($data), 'options' => new ArrayObject()]);
        $this->assertNull($return->getResult());
        // コンテンツのバリデーションに失敗する時はfalseが返る
        $notEmptyResult = ["" => false, "test" => null];
        foreach ($notEmptyResult as $nameField => $result) {
            $data = [
                'folder_template' => 'テストBeforeSave',
                'content' => [
                    "name" => $nameField,
                    "parent_id" => "1",
                    "title" => "新しい フォルダー",
                    "plugin" => 'BaserCore',
                    "type" => "ContentFolder",
                    "site_id" => "0",
                    "alias_id" => "",
                    "entity_id" => "",
                ]
            ];
            $data = $this->table->dispatchEvent('Model.beforeMarshal', [
                'data' => new ArrayObject($data),
                'options' => new ArrayObject(),
            ]);
            $this->assertEquals($result, $data->getResult());
        }
        // Contents->beforeMarshalでデータが補填されているかを確認する
        $array = (array) $data->getData('data')['content'];
        $this->assertEquals(15, count($array));
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
        $data = new Entity([
                'folder_template' => 'テストBeforeSave',
                'content' => [
                    "name" => "", // validation error
                    "parent_id" => "1",
                    "title" => "新しい フォルダー",
                    "plugin" => 'BaserCore',
                    "type" => "ContentFolder",
                    "site_id" => "0",
                    "alias_id" => "",
                    "entity_id" => "",
                ]
        ]);
        $this->assertFalse($this->table->save($data));
        // TODO: イベント設定うまくいかないので調整する
        // $listener = function ($event, $entity, $options) {
        //     $options['validate'] = false;
        // };
        // $this->table->getEventManager()->on('Model.beforeSave', $listener);
        // $this->assertTrue($this->table->save($data));

    }

    /**
     * After save
     *
     * Content を保存する
     */
    public function testAfterSave()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $data = new Entity([
            'folder_template' => 'テストBeforeSave',
            'content' => [
                "parent_id" => "1",
                "title" => "新しい フォルダー",
                "plugin" => 'BaserCore',
                "type" => "ContentFolder",
                "site_id" => "0",
                "alias_id" => "",
                "entity_id" => "",
            ]
    ]);
        $this->assertTrue($this->table->save($data));
    }

    /**
     * Before delete
     *
     * afterDelete でのContents物理削除準備をする
     */
    public function testBeforeDelete()
    {
        $event = $this->table->dispatchEvent('Model.beforeDelete', [
            'entity' => $this->table->get(10),
            'options' => new ArrayObject(),
        ]);
        $this->assertNotEmpty($event->getData('entity')->content);
    }
    /**
     * After delete
     *
     * 削除したデータに連携する Content を削除
     */
    public function testAfterDelete()
    {
        $entity = $this->table->get(10);
        $entity->content = $this->contentService->getTrash(16);
        $event = $this->table->dispatchEvent('Model.afterDelete', [
            'entity' => $entity,
            'options' => new ArrayObject(),
        ]);
        $this->assertTrue($this->table->Contents->find()->where(['entity_id' => 10])->isEmpty());

    }

    /**
     * 公開されたコンテンツを取得する
     */
    public function testFindPublished()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
