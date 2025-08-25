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

namespace BcCustomContent\Test\TestCase\Model\Table;

use ArrayObject;
use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Service\PluginsServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Model\Table\CustomContentsTable;
use BcCustomContent\Service\CustomContentsService;
use BcCustomContent\Test\Factory\CustomContentFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use BcCustomContent\Service\CustomContentsServiceInterface;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Test\Scenario\CustomTablesScenario;
use Cake\Event\Event;

/**
 * CustomContentsTableTest
 * @property CustomContentsTable $CustomContentsTable
 */
class CustomContentsTableTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomContentsTable = $this->getTableLocator()->get('BcCustomContent.CustomContents');
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomContentsTable);
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertTrue($this->CustomContentsTable->hasBehavior('BcContents'));
        $this->assertTrue($this->CustomContentsTable->hasBehavior('Timestamp'));
        $this->assertTrue($this->CustomContentsTable->hasBehavior('BcContents'));
        $this->assertTrue($this->CustomContentsTable->hasAssociation('CustomTables'));
    }

    /**
     * test validationWithTable
     */
    public function test_validationWithTable()
    {
        //全角文字を入力した場合
        $validator = $this->CustomContentsTable->getValidator('withTable');
        $errors = $validator->validate([
            'list_count' => '漢字'
        ]);
        $this->assertEquals([
            'range' => '一覧表示件数は100までの数値で入力してください。',
            'halfText' => '一覧表示件数は半角で入力してください。'
        ], $errors['list_count']);

        //101を入力した場合
        $validator = $this->CustomContentsTable->getValidator('withTable');
        $errors = $validator->validate([
            'list_count' => '101'

        ]);
        $this->assertEquals([
            'range' => '一覧表示件数は100までの数値で入力してください。',
        ], $errors['list_count']);

        //何も入力しない場合
        $validator = $this->CustomContentsTable->getValidator('withTable');
        $errors = $validator->validate([
            'list_count' => ''
        ]);
        $this->assertEquals([
            '_empty' => '一覧表示件数は必須項目です。',
        ], $errors['list_count']);
    }

    /**
     * test createSearchIndex
     */
    public function test_createSearchIndex()
    {
        //content empty
        $entity = CustomContentFactory::make([
            'id' => 1,
        ])->getEntity();
        $this->assertFalse($this->CustomContentsTable->createSearchIndex($entity));

        //The system operates normally
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $customContentService = new CustomContentsService();
        $customContent = $customContentService->get(1);

        //set content publish_begin and publish_end
        $customContent->content->publish_begin = '2021-01-01 00:00:00';
        $customContent->content->publish_end = '2021-12-31 23:59:59';

        $rs = $this->CustomContentsTable->createSearchIndex($customContent);
        $this->assertEquals($rs['type'], 'カスタムコンテンツ');
        $this->assertEquals($rs['model_id'], 1);
        $this->assertEquals($rs['content_id'], 100);
        $this->assertEquals($rs['site_id'], 1);
        $this->assertEquals($rs['title'], 'サービスタイトル');
        $this->assertEquals($rs['detail'], 'サービステスト');
        $this->assertEquals($rs['url'], '/test/');
        $this->assertTrue($rs['status']);
        $this->assertEquals($rs['publish_begin'], '2021-01-01 00:00:00');
        $this->assertEquals($rs['publish_end'], '2021-12-31 23:59:59');
    }

    /**
     * test createRelatedSearchIndexes
     */
    public function test_createRelatedSearchIndexes()
    {
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomTablesScenario::class);
        $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customEntriesService->setUp(2);

        $customEntriesService->create([
            'custom_table_id' => 2,
            'name' => 'テスト職種1',
            'title' => 'テスト職種1',
            'status' => 1,
            'creator_id' => 1
        ]);
        $customEntriesService->create([
            'custom_table_id' => 2,
            'name' => 'テスト職種2',
            'title' => 'テスト職種2',
            'status' => 1,
            'creator_id' => 1
        ]);

        $customContentService = new CustomContentsService();
        $customContent = $customContentService->get(2);

        $this->CustomContentsTable->createRelatedSearchIndexes($customContent);

        $searchIndexesTable = $this->getTableLocator()->get('BcSearchIndex.SearchIndexes');
        $this->assertEquals(2, $searchIndexesTable->find()->count());

        $dataBaseService->dropTable('custom_entry_2_occupations');
    }

    /**
     * test beforeSave
     */
    public function test_beforeSave()
    {
        //サービスクラス
        $PluginsService = $this->getService(PluginsServiceInterface::class);
        $customContentService = $this->getService(CustomContentsServiceInterface::class);
        $PluginsService->attach('BcSearchIndex');

        $this->loadFixtureScenario(CustomContentsScenario::class);

        //isExcluded false
        $customContent = $customContentService->get(1);
        $this->CustomContentsTable->beforeSave(new Event("beforeSave"), $customContent, new ArrayObject());
        $this->assertFalse($this->CustomContentsTable->isExcluded());

        //isExcluded true
        $customContent = $customContentService->get(1);
        $customContent->content->exclude_search = 1;
        $this->CustomContentsTable->beforeSave(new Event("beforeSave"), $customContent, new ArrayObject());
        $this->assertTrue($this->CustomContentsTable->isExcluded());
    }
}
