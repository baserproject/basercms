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

namespace BaserCore\Test\TestCase\View\Helper;

use Cake\Routing\Router;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcAdminContentHelper;

/**
 * Class BcAdminContentHelperTest
 *
 *
 * @package BaserCore\Test\TestCase\View\Helper
 */
class BcAdminContentHelperTest extends \BaserCore\TestSuite\BcTestCase
{
    /**
     * BcAdminContentHelper
     * @var BcAdminContentHelper
     */

    public $BcAdminContent;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.Contents',
    ];

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAdminContent = new BcAdminContentHelper(new BcAdminAppView($this->getRequest('/')));
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        Router::reload();
        unset($this->BcAdminContent);
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->assertTrue(isset($this->BcAdminContent->ContentService));
    }

    /**
     * testGetType
     *
     * @return void
     */
    public function testGetType(): void
    {
        $expected = [
            'Default' => '無所属コンテンツ',
            'ContentFolder' => 'フォルダー',
            'ContentAlias' => 'エイリアス',
            'ContentLink' => 'リンク',
            'Page' => '固定ページ',
            'BlogContent' => 'ブログ'
        ];
        $this->assertEquals($expected, $this->BcAdminContent->getTypes());
    }

    /**
     * testIsContentDeletable
     *
     * @param  int $id
     * @param  bool $expected
     * @return void
     * @dataProvider isContentDeletableDataProvider
     */
    public function testIsContentDeletable($id, $expected): void
    {
        Router::setRequest($this->loginAdmin($this->getRequest(), $id));
        $this->assertEquals($expected, $this->BcAdminContent->isContentDeletable());
    }
    public function isContentDeletableDataProvider()
    {
        return [
            // [1, true],
            [2, true],
            // [3, false],
        ];
    }

    /**
     * サイトIDからコンテンツIDを取得する
     * getSiteRootId
     *
     * @param int $siteId
     * @param string|bool $expect 期待値
     * @dataProvider getSiteRootIdDataProvider
     */
    public function testGetSiteRootId($siteId, $expect)
    {
        $result = $this->BcAdminContent->getSiteRootId($siteId);
        $this->assertEquals($expect, $result);
    }
    public function getSiteRootIdDataProvider()
    {
        return [
            // 存在するサイトID（0~2）を指定した場合
            [1, 1],
            // 存在しないサイトIDを指定した場合
            [4, false],
        ];
    }
}
