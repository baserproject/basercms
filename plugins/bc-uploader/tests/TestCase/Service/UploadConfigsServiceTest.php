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

namespace BcUploader\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BcUploader\Model\Entity\UploaderConfig;
use BcUploader\Model\Table\UploaderConfigsTable;
use BcUploader\Service\UploaderConfigsService;
use BcUploader\Service\UploaderConfigsServiceInterface;
use BcUploader\Test\Factory\UploaderConfigFactory;
use BcUploader\Test\Scenario\UploaderFilesScenario;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * UploadConfigsServiceTest
 * @property UploaderConfigsService $UploaderConfigsService
 *
 */
class UploadConfigsServiceTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UploaderConfigsService = $this->getService(UploaderConfigsServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $this->truncateTable('uploader_files');
        $this->truncateTable('uploader_categories');
    }

    /**
     * test __construct
     */
    public function test_construct()
    {
        $this->UploaderConfigsService->__construct();
        $this->assertInstanceOf(UploaderConfigsTable::class, $this->UploaderConfigsService->UploaderConfigs);
    }

    /**
     * test get
     */
    public function test_get()
    {
        //準備
        //データを生成
        UploaderConfigFactory::make(['name' => 'name_1', 'value' => 'value_1'])->persist();
        //正常系実行
        $result = $this->UploaderConfigsService->get();
        $this->assertInstanceOf(UploaderConfig::class, $result);
        $this->assertEquals('value_1', $result->name_1);

    }

    /**
     * test clearCache
     */
    public function test_clearCache()
    {
        UploaderConfigFactory::make(['name' => 'name_1', 'value' => 'value_1'])->persist();

        //実行前の確認
        $this->UploaderConfigsService->get();
        $entity = $this->getPrivateProperty($this->UploaderConfigsService, 'entity');
        $this->assertNotNull($entity);

        //正常系実行
        $this->UploaderConfigsService->clearCache();
        $result = $this->getPrivateProperty($this->UploaderConfigsService, 'entity');
        $this->assertNull($result);
    }

    /**
     * test update
     */
    public function test_update()
    {
        //準備
        $this->loadFixtureScenario(UploaderFilesScenario::class);
        $UploaderConfigs = TableRegistry::getTableLocator()->get('BcUploader.UploaderConfigs');
        //アップデート前の確認
        $rs = $UploaderConfigs->find()->where(['name' => 'large_width'])->first();
        $this->assertEquals(500, $rs->value);
        //正常系実行
        $postData = [
            'large_width' => 600
        ];
        $result = $this->UploaderConfigsService->update($postData);
        $this->assertEquals(600, $result->large_width);
        //アップデート後の確認
        $rs = $UploaderConfigs->find()->where(['name' => 'large_width'])->first();
        $this->assertEquals(600, $rs->value);

    }


}
