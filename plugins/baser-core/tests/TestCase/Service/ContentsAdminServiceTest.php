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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\ContentsAdminService;
use BaserCore\Service\ContentsAdminServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\Routing\Router;

/**
 * Class ContentsAdminServiceTest
 */
class ContentsAdminServiceTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * ContentsAdminService
     * @var ContentsAdminService
     */

    public $ContentsAdmin;

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
        'plugin.BaserCore.Sites',
    ];

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentsAdmin = $this->getService(ContentsAdminServiceInterface::class);
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        Router::reload();
        unset($this->ContentsAdmin);
        parent::tearDown();
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
            'Page' => '固定ページ',
            'BlogContent' => 'ブログ'
        ];
        $this->assertEquals($expected, $this->ContentsAdmin->getTypes());
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
        $this->assertEquals($expected, $this->ContentsAdmin->isContentDeletable());
    }

    public function isContentDeletableDataProvider()
    {
        return [
            [1, true],
            [2, true],
            [3, false],
        ];
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        $vars = $this->ContentsAdmin->getViewVarsForEdit($this->ContentsAdmin->get(5));
        $this->assertTrue(isset($vars['parentContents']));
        $this->assertTrue(isset($vars['fullUrl']));
        $this->assertTrue(isset($vars['authorList']));
    }

    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        $vars = $this->ContentsAdmin->getViewVarsForIndex();
        $this->assertTrue(isset($vars['typeList']));
        $this->assertTrue(isset($vars['authorList']));
        $this->assertTrue(isset($vars['isContentDeletable']));
    }

}
