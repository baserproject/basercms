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

namespace BaserCore\Test\TestCase\Service\Admin;

use BaserCore\Service\Admin\ContentsAdminService;
use BaserCore\Service\Admin\ContentsAdminServiceInterface;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Scenario\ContentFoldersScenario;
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\PermissionsScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\Test\Scenario\SitesScenario;
use BaserCore\Test\Scenario\UserGroupsScenario;
use BaserCore\Test\Scenario\UserScenario;
use BaserCore\Test\Scenario\UsersUserGroupsScenario;
use BaserCore\Utility\BcContainerTrait;
use Cake\Routing\Router;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

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
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * ContentsAdminService
     * @var ContentsAdminService
     */

    public $ContentsAdmin;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(UserScenario::class);
        $this->loadFixtureScenario(UserGroupsScenario::class);
        $this->loadFixtureScenario(UsersUserGroupsScenario::class);
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(PermissionsScenario::class);
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
            'ContentFolder' => 'フォルダー',
            'Page' => '固定ページ',
            'ContentAlias' => 'エイリアス',
            'BlogContent' => 'ブログ',
            'ContentLink' => 'リンク',
            'CustomContent' => 'カスタムコンテンツ',
            'MailContent' => 'メールフォーム'
        ];
        $this->assertEquals($expected, $this->ContentsAdmin->getTypes());
    }

    /**
     * testIsContentDeletable
     *
     * @param int $id
     * @param bool $expected
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
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        $request = $this->getRequest('/baser/admin/baser-core/contents/index?list_type=1');
        $this->loginAdmin($request);
        $vars = $this->ContentsAdmin->getViewVarsForIndex($request, $this->ContentsAdmin->get(5));
        $this->assertTrue(isset($vars['typeList']));
        $this->assertTrue(isset($vars['authorList']));
        $this->assertTrue(isset($vars['isContentDeletable']));
        $this->assertTrue(isset($vars['folders']));
        $this->assertTrue(isset($vars['template']));
        $this->assertTrue(isset($vars['editInIndexDisabled']));
        $this->assertTrue(isset($vars['isUseMoveContents']));
        $this->assertTrue(isset($vars['contents']));
        $this->assertTrue(isset($vars['contentsSearch']));
    }

    /**
     * test getViewVarsForContentActions
     */
    public function test_getViewVarsForContentActions()
    {
        $request = $this->getRequest('/baser/admin/baser-core/contents/index?list_type=1');
        $this->loginAdmin($request);
        $result = $this->ContentsAdmin->getViewVarsForContentActions($this->ContentsAdmin->get(5), null);
        $this->assertArrayHasKey('isAvailablePreview', $result);
        $this->assertArrayHasKey('isAvailableDelete', $result);
        $this->assertArrayHasKey('currentAction', $result);
        $this->assertArrayHasKey('isAlias', $result);
    }

    /**
     * test _isAvailablePreview
     */
    public function test_isAvailablePreview()
    {
        $content = $this->ContentsAdmin->get(1);
        $result = $this->execPrivateMethod($this->ContentsAdmin, '_isAvailablePreview', [$content]);
        $this->assertTrue($result);

        $content = $this->ContentsAdmin->get(14);
        $result = $this->execPrivateMethod($this->ContentsAdmin, '_isAvailablePreview', [$content]);
        $this->assertFalse($result);
    }

    /**
     * test _isAvailableDelete
     */
    public function test_isAvailableDelete()
    {
        $request = $this->getRequest('/baser/admin/baser-core/contents/index?list_type=1');
        $this->loginAdmin($request);

        $content = $this->ContentsAdmin->get(1);
        $result = $this->execPrivateMethod($this->ContentsAdmin, '_isAvailableDelete', [$content]);
        $this->assertFalse($result);

        $content = $this->ContentsAdmin->get(4);
        $result = $this->execPrivateMethod($this->ContentsAdmin, '_isAvailableDelete', [$content]);
        $this->assertTrue($result);

        UserFactory::make(['id' => 10])->persist();
        $this->loginAdmin($request, 10);
        $content = $this->ContentsAdmin->get(10);
        $result = $this->execPrivateMethod($this->ContentsAdmin, '_isAvailableDelete', [$content]);
        $this->assertFalse($result);
    }

    /**
     * test getViewVarsForTrashIndex
     */
    public function test_getViewVarsForTrashIndex()
    {
        $result = $this->ContentsAdmin->getViewVarsForTrashIndex($this->ContentsAdmin->get(5));
        $this->assertArrayHasKey('contents', $result);
        $this->assertArrayHasKey('isContentDeletable', $result);
        $this->assertArrayHasKey('isUseMoveContents', $result);
        $this->assertArrayHasKey('editInIndexDisabled', $result);
    }
}
