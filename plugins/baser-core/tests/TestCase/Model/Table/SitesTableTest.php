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

namespace BaserCore\Test\TestCase\Model\Table;

use ArrayObject;
use BaserCore\Model\Table\SitesTable;
use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;
use Cake\Event\Event;
use ReflectionClass;

/**
 * Class SitesTableTest
 * @package BaserCore\Test\TestCase\Model\Table
 * @property SitesTable $Sites
 */
class SitesTableTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Sites',
    ];

    /**
     * Set Up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Sites = $this->getTableLocator()->get('BaserCore.Sites');
    }

    /**
     * Tear Down
     */
    public function tearDown(): void
    {
        unset($this->Sites);
        parent::tearDown();
    }

    /**
     * 公開されている全てのサイトを取得する
     */
    public function testGetPublishedAll()
    {
        $this->assertEquals(4, count($this->Sites->getPublishedAll()));
        $site = $this->Sites->find()->where(['id' => 2])->first();
        $site->status = true;
        $this->Sites->save($site);
        $this->assertEquals(5, count($this->Sites->getPublishedAll()));
    }

    /**
     * サイトリストを取得
     *
     * @param int $mainSiteId メインサイトID
     * @param array $options
     * @param array $expects
     * @param string $message
     * @dataProvider getSiteListDataProvider
     */
    public function testGetSiteList($mainSiteId, $options, $expects, $message)
    {
        $result = $this->Sites->getSiteList($mainSiteId, $options);
        $this->assertEquals($expects, $result, $message);
    }

    public function getSiteListDataProvider()
    {
        return [
            [null, [], [1 => 'メインサイト', 3 => '英語サイト', 4 => '別ドメイン', 5 => 'サブドメイン'], '全てのサイトリストの取得ができません。'],
            [1, [], [3 => '英語サイト', 4 => '別ドメイン', 5 => 'サブドメイン'], 'メインサイトの指定ができません。'],
            [2, [], [], 'メインサイトの指定ができません。'],
            [null, ['excludeIds' => [1]], [3 => '英語サイト', 4 => '別ドメイン', 5 => 'サブドメイン'], '除外指定ができません。'],
            [null, ['excludeIds' => 1], [3 => '英語サイト', 4 => '別ドメイン', 5 => 'サブドメイン'], '除外指定ができません。'],
            [null, ['includeIds' => [1, 2], 'status' => null], [1 => 'メインサイト', 2 => 'スマホサイト'], 'ID指定ができません。'],
            [null, ['status' => false], [2 => 'スマホサイト'], 'ステータス指定ができません。'],
        ];
    }

    /**
     * メインサイトのデータを取得する
     */
    public function testGetRootMain()
    {
        $this->assertEquals(1, $this->Sites->getRootMain()['id']);
        $this->assertEquals(2, count($this->Sites->getRootMain(['fields' => ['name', 'display_name']])));
    }

    /**
     * コンテンツに関連したコンテンツをサイト情報と一緒に全て取得する
     */
    public function testGetRelatedContents()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * メインサイトかどうか判定する
     */
    public function testIsMain()
    {
        $this->assertTrue($this->Sites->isMain(1));
        $this->assertFalse($this->Sites->isMain(2));
    }

    /**
     * サブサイトを取得する
     */
    public function testChildren()
    {
        $this->assertEquals(4, $this->Sites->children(1)->count());
        $this->assertEquals(0, $this->Sites->children(2)->count());
        $this->assertEquals(3, $this->Sites->children(1, ['conditions' => ['status' => true]])->count());
    }

    /**
     * After Save
     */
    public function testAfterSave()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * After Delete
     */
    public function testAfterDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * プレフィックスを取得する
     */
    public function testGetPrefix()
    {
        $this->assertEquals('', $this->Sites->getPrefix(1));
        $this->assertEquals('s', $this->Sites->getPrefix(2));
        $this->assertEquals(false, $this->Sites->getPrefix(6));
    }

    /**
     * サイトのルートとなるコンテンツIDを取得する
     */
    public function testGetRootContentId()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * URLよりサイトを取得する
     * @dataProvider findByUrlDataProvider
     */
    public function testFindByUrl($url, $expected)
    {
        $site = $this->Sites->findByUrl($url);
        $this->assertEquals($expected, $site['id']);
    }

    public function findByUrlDataProvider() {
        return [
            ['', 1],
            ['/s/index', 2],
            ['/en/about', 3],
            ['/test/a', 1]
        ];
    }

    /**
     * メインサイトを取得する
     */
    public function testGetMain()
    {
        $this->assertEquals(1, $this->Sites->getMain(1)['id']);
        $this->assertEquals(1, $this->Sites->getMain(2)['id']);
        $this->assertEquals(false, $this->Sites->getMain(6));
    }

    /**
     * 選択可能なデバイスの一覧を取得する
     */
    public function testGetSelectableDevices()
    {
        $this->assertEquals(2, count($this->Sites->getSelectableDevices(1, 3)));
        $this->Sites->delete($this->Sites->get(2));
        $this->assertEquals(3, count($this->Sites->getSelectableDevices(1, 3)));
    }

    /**
     * 選択可能が言語の一覧を取得する
     */
    public function testGetSelectableLangs()
    {
        $this->assertEquals(3, count($this->Sites->getSelectableLangs(1, 2)));
        $this->Sites->delete($this->Sites->get(3));
        $this->assertEquals(4, count($this->Sites->getSelectableLangs(1, 2)));
    }

    /**
     * testResetDevice
     */
    public function testResetDevice()
    {
        $this->Sites->resetDevice();
        $sites = $this->Sites->find()->all();
        foreach($sites as $site) {
            $this->assertEquals($site->device, '');
            $this->assertFalse($site->auto_link);
            if (!$site->lang) {
                $this->assertFalse($site->same_main_url);
                $this->assertFalse($site->auto_redirect);
            }
        }
    }

    /**
     * testResetDevice
     */
    public function testResetLang()
    {
        $this->Sites->resetLang();
        $sites = $this->Sites->find()->all();
        foreach($sites as $site) {
            $this->assertEquals($site->lang, '');
            if (!$site->device) {
                $this->assertFalse($site->same_main_url);
                $this->assertFalse($site->auto_redirect);
            }
        }
    }

    /**
     * Before Save
     */
    public function testBeforeSave()
    {
        $site = $this->Sites->find()->where(['id' => 2])->first();
        $site->alias = 'm';
        $this->Sites->beforeSave(new Event('beforeSave'), $site, new ArrayObject());
        $reflectionClass = new ReflectionClass(get_class($this->Sites));
        $property = $reflectionClass->getProperty('__changedAlias');
        $property->setAccessible(true);
        $this->assertTrue($property->getValue($this->Sites));
    }

}
