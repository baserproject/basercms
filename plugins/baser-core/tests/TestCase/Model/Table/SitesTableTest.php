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
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
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
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
    ];

    /**
     * Set Up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Sites = $this->getTableLocator()->get('BaserCore.Sites');
        $this->Contents = $this->getTableLocator()->get('BaserCore.Contents');
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
        $this->assertEquals(5, count($this->Sites->getPublishedAll()));
        $site = $this->Sites->find()->where(['id' => 2])->first();
        $site->status = true;
        $this->Sites->save($site);
        $this->assertEquals(6, count($this->Sites->getPublishedAll()));
    }

    /**
     * サイトリストを取得
     *
     * @param int $mainSiteId メインサイトID
     * @param array $options
     * @param array $expects
     * @param string $message
     * @dataProvider getListDataProvider
     */
    public function testGetList($mainSiteId, $options, $expects, $message)
    {
        $result = $this->Sites->getList($mainSiteId, $options);
        $this->assertEquals($expects, $result, $message);
    }

    public function getListDataProvider()
    {
        return [
//            [null, [], [1 => 'メインサイト', 3 => '英語サイト', 4 => '別ドメイン', 5 => 'サブドメイン'], '全てのサイトリストの取得ができません。'],
//            [1, [], [3 => '英語サイト', 4 => '別ドメイン', 5 => 'サブドメイン'], 'メインサイトの指定ができません。'],
//            [2, [], [], 'メインサイトの指定ができません。'],
//            [null, ['excludeIds' => [1]], [3 => '英語サイト', 4 => '別ドメイン', 5 => 'サブドメイン'], '除外指定ができません。'],
//            [null, ['excludeIds' => 1], [3 => '英語サイト', 4 => '別ドメイン', 5 => 'サブドメイン'], '除外指定ができません。'],
            [null, ['includeIds' => [1, 2], 'status' => null], [1 => 'メインサイト', 2 => 'スマホサイト'], 'ID指定ができません。'],
            [null, ['status' => false], [2 => 'スマホサイト'], 'ステータス指定ができません。'],
        ];
    }

    /**
     * メインサイトのデータを取得する
     */
    public function testGetRootMain()
    {
        $this->assertEquals(1, $this->Sites->getRootMain()->id);
    }

    /**
     * コンテンツに関連したコンテンツをサイト情報と一緒に全て取得する
     */
    public function testGetRelatedContents()
    {
        $list = $this->Sites->getRelatedContents(24);
        $this->assertCount(6, $list);
        $sample = array_shift($list);
        $this->assertNotEmpty($sample['Site']);
        $this->assertNotEmpty($sample['Content']);
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
        $this->assertEquals(5, $this->Sites->children(1)->count());
        $this->assertEquals(0, $this->Sites->children(2)->count());
        $this->assertEquals(4, $this->Sites->children(1, ['conditions' => ['status' => true]])->count());
    }

    /**
     * After Save
     */
    public function testAfterSave()
    {
        $this->loginAdmin($this->getRequest());
        $site = $this->Sites->get(2);
        $this->Sites->dispatchEvent('Model.afterSave', [$site, new ArrayObject()]);
        $updatedContent = $this->Contents->find()->where(['site_id' => 2, 'site_root' => true])->first();
        $this->assertEquals($updatedContent->name, $site->alias);
    }

    /**
     * After Delete
     */
    public function testAfterDelete()
    {
        $this->loginAdmin($this->getRequest());
        $site = $this->Sites->get(3);
        $contents = $this->Contents->find()->where(['site_id' => 3])->all();
        $enSiteFolder = $contents->first();
        $enSitePage = $contents->last();
        $this->Sites->dispatchEvent('Model.afterDelete', [$site, new ArrayObject()]);
        // 削除対象のサイトIDがページでメインサイトに書き換わっているか | また論理削除されてるかを確認
        $page = $this->Contents->find()->where(['id' => $enSitePage->id, 'deleted_date IS NOT' => null])->applyOptions(['withDeleted'])->first();
        $this->assertEquals(1, $page->site_id);
        // 削除対象サイトIDのフォルダーが完全に削除されてるか
        $folder = $this->Contents->find()->where(['id' => $enSiteFolder->id, 'deleted_date IS NOT' => null])->applyOptions(['withDeleted'])->first();
        $this->assertNull($folder);
    }

    /**
     * プレフィックスを取得する
     */
    public function testGetPrefix()
    {
        $this->assertEquals('', $this->Sites->getPrefix(1));
        $this->assertEquals('s', $this->Sites->getPrefix(2));
        $this->assertEquals(false, $this->Sites->getPrefix(7));
    }

    /**
     * サイトのルートとなるコンテンツIDを取得する
     */
    public function testGetRootContentId()
    {
        $this->assertEquals(1, $this->Sites->getRootContentId(0));
        $this->assertEquals(1, $this->Sites->getRootContentId(1));
        $this->assertEquals(23, $this->Sites->getRootContentId(2));
        $this->assertEquals(24, $this->Sites->getRootContentId(3));
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->Sites->getRootContentId(100);
    }

    /**
     * URLよりサイトを取得する
     * @dataProvider findByUrlDataProvider
     */
    public function testFindByUrl($url, $expected)
    {
        $this->getRequest($url);
        $site = $this->Sites->findByUrl($url);
        $this->assertEquals($expected, $site->id);
    }

    public function findByUrlDataProvider()
    {
        return [
            ['', 1],
            ['/s/index', 2],
            ['/s/test', 2],
            ['/en/index', 3],
            ['/en/about', 3],
            ['/test/a', 1], // 存在しない場合はルートメインサイトを返す
            ['http://basercms.net/about', 4],
            ['http://sub.localhost/about', 5]
        ];
    }

    /**
     * test getMainByUrl
     */
    public function testGetMainByUrl()
    {
        $site = $this->Sites->getMainByUrl('/');
        $this->assertNull($site);
        $site = $this->Sites->getMainByUrl('/en/');
        $this->assertEquals(1, $site->id);
    }


    /**
     * test getSubByUrl
     */
    public function testGetSubByUrl()
    {
        // スマホ
        $siteConfigs = TableRegistry::getTableLocator()->get('BaserCore.SiteConfigs');
        $siteConfigs->saveValue('use_site_device_setting', true);
        $_SERVER['HTTP_USER_AGENT'] = 'iPhone';
        $site = $this->Sites->get(2);

        $site = $this->Sites->patchEntity($site, ['status' => true]);
        $this->Sites->save($site);
        $site = $this->Sites->getSubByUrl('/');
        $this->assertEquals('s', $site->alias);

        // 英語
        $siteConfigs->saveValue('use_site_lang_setting', true);
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en';
        $site = $this->Sites->getSubByUrl('/');
        $this->assertEquals('en', $site->alias);
    }

    /**
     * メインサイトを取得する
     */
    public function testGetMain()
    {
        $this->assertEquals(1, $this->Sites->getMain(1)->id);
        $this->assertEquals(1, $this->Sites->getMain(2)->id);
        $this->assertEquals(false, $this->Sites->getMain(7));
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
        $property = $reflectionClass->getProperty('changedAlias');
        $property->setAccessible(true);
        $this->assertTrue($property->getValue($this->Sites));
    }

}
