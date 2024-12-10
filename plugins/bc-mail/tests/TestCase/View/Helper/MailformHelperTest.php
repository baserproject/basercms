<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */
namespace BcMail\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcMail\Model\Entity\MailField;
use BcMail\Test\Factory\MailMessagesFactory;
use BcMail\Service\MailFieldsServiceInterface;
use BcMail\View\Helper\MailformHelper;
use BcMail\Test\Scenario\MailContentsScenario;
use BcMail\Test\Scenario\MailFieldsScenario;
use Cake\ORM\ResultSet;
use Cake\View\View;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class MailformHelperTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->MailformHelper = new MailformHelper(new View());
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * メールフィールドのデータよりコントロールを生成する
     */
    public function testControl()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * create
     * ファイル添付の対応のためにデフォルト値を変更
     */
    public function testCreate()
    {
        $rs = $this->MailformHelper->create();
        $this->assertTextContains('form enctype="multipart/form-data"', $rs);

        $rs = $this->MailformHelper->create(MailMessagesFactory::make()->getEntity(), ['url' => '/test']);
        $this->assertTextContains('action="/test"', $rs);
    }

    /**
     * 認証キャプチャを表示する
     */
    public function testAuthCaptcha()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test isGroupLastField
     * @param array $mailFieldsData
     * @param int $currentFieldIndex
     * @param bool $expected
     * @dataProvider isGroupLastFieldProvider
     */
    public function testIsGroupLastField($mailFieldsData, $currentFieldIndex, $expected)
    {
        //create ResultSet
        $mailFields = new ResultSet(array_map(function ($item) {
            return new MailField($item);
        }, $mailFieldsData));

        //get current mail field
        $currentMailField = $mailFields->toArray()[$currentFieldIndex];

        $result = $this->MailformHelper->isGroupLastField($mailFields, $currentMailField);
        $this->assertEquals($expected, $result);
    }

    public static function isGroupLastFieldProvider()
    {
        return [
        [[['group_field' => 'group1'], ['group_field' => 'group1'], ['group_field' => 'group2']], 1, true],
        [[['group_field' => 'group1'], ['group_field' => 'group1'], ['group_field' => 'group1']], 1, false],
        [[['group_field' => 'group1'], ['group_field' => 'group1'], ['group_field' => 'group1']], 2, true],
        [[['group_field' => ''], ['group_field' => 'group1']], 0, false],
        [[['group_field' => 'group1'], ['group_field' => 'group2']], 0, true],
    ];
    }

    /**
     * test getGroupValidErrors
     */
    public function testGetGroupValidErrors()
    {
        // prepare
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $MailFieldsService = $this->getService(MailFieldsServiceInterface::class);
        // get mail field list
        $mailFields = $MailFieldsService->getIndex(1)->all();
        $rs = $this->MailformHelper->getGroupValidErrors($mailFields, 'field_name');
        $this->assertEquals([], $rs);
    }
}
