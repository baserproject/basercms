<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcContentLink\Test\TestCase\Model\Table;

use BcContentLink\Test\Factory\BlogTagFactory;
use BcContentLink\Model\Table\ContentLinksTable;
use BaserCore\TestSuite\BcTestCase;
use BcContentLink\Test\Factory\ContentLinkFactory;
use BcContentLink\Test\Scenario\ContentLinksServiceScenario;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class ContentLinksTableTest
 * @property ContentLinksTable $ContentLinks
 */
class ContentLinksTableTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentLinks = $this->getTableLocator()->get('BcContentLink.ContentLinks');

    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentLinks);
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->assertTrue($this->ContentLinks->hasBehavior('BcContents'));
    }

    /**
     * Test validationDefault
     */
    public function testValidationDefault()
    {
        $contentLink = $this->ContentLinks->newEntity(['id' => 'test']);
        $this->assertSame([
            'id' => [
                'integer' => 'The provided value must be an integer',
            ],
            // BcContentsBehaviorのafterMarshalにて、contentを他のフィールド同様必要前提としている
            'content' => [
                '_required' => '関連するコンテンツがありません'
            ]
        ], $contentLink->getErrors());
    }

    /**
     * Test testValidationURL
     */
    public function testValidationURL()
    {
        $validator = $this->ContentLinks->getValidator('default');
        //エラー場合、
        //スペースだけ入力
        $errors = $validator->validate([
            'url' => '        '
        ]);
        //戻り値を確認
        $this->assertEquals('リンク先URLはURLの形式を入力してください。', current($errors['url']));
        //スラッシュがない場合
        $errors = $validator->validate([
            'url' => 'あああああ'
        ]);
        //戻り値を確認
        $this->assertEquals('リンク先URLはURLの形式を入力してください。', current($errors['url']));

        //長いURLを入力場合
        $errors = $validator->validate([
            'url' => '/' . str_repeat('a', 255)
        ]);
        //戻り値を確認
        $this->assertEquals('リンク先URLは255文字以内で入力してください。', current($errors['url']));

        //正常場合、
        $errors = $validator->validate([
            'url' => '/test'
        ]);
        //戻り値を確認
        $this->assertCount(0, $errors);

        $errors = $validator->validate([
            'url' => 'https://basercms.net/'
        ]);
        //戻り値を確認
        $this->assertCount(0, $errors);
    }

    /**
     * test beforeCopyEvent
     */
    public function testBeforeCopyEvent()
    {
        $this->loadFixtureScenario(ContentLinksServiceScenario::class);
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_MODEL, 'BcContentLink.ContentLinks.beforeCopy', function (Event $event) {
            $data = $event->getData('data');
            $data['url'] = '/beforeCopy';
            $event->setData('data', $data);
        });
        $this->ContentLinks->copy(1, 1, 'new title', 1, 1);
        //イベントに入るかどうか確認
        $contentLinks = $this->getTableLocator()->get('BcContentLink.ContentLinks');
        $query = $contentLinks->find()->where(['url' => '/beforeCopy']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * test AfterCopyEvent
     */
    public function testAfterCopyEvent()
    {
        $this->loadFixtureScenario(ContentLinksServiceScenario::class);
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_MODEL, 'BcContentLink.ContentLinks.afterCopy', function (Event $event) {
            $data = $event->getData('data');
            $contentLinks = TableRegistry::getTableLocator()->get('BcContentLink.ContentLinks');
            $data->url = '/AfterCopy';
            $contentLinks->save($data);
        });
        $this->ContentLinks->copy(1, 1, 'new title', 1, 1);
        //イベントに入るかどうか確認
        $contentLinks = $this->getTableLocator()->get('BcContentLink.ContentLinks');
        $query = $contentLinks->find()->where(['url' => '/AfterCopy']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * test copy
     */
    public function test_copy()
    {
        //データを生成
        $this->loadFixtureScenario(ContentLinksServiceScenario::class);
        //コピーメソッドをコール
        $rs = $this->ContentLinks->copy(1, 2, 'new title', 1, 2);
        //戻る値を確認
        $this->assertEquals('new title', $rs->content->title);
        $this->assertEquals(1, $rs->content->parent_id);
        $this->assertEquals(1, $rs->content->author_id);
        $this->assertEquals(2, $rs->content->site_id);
        $this->assertEquals(2, $rs->id);

        //DBに存在するか確認
        $copiedContentLink = $this->ContentLinks->get(2, contain: ['Contents']);
        //コピー後の url の値の確認
        $this->assertEquals('/new_title', $copiedContentLink->content->url);
    }
}
