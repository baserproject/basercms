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

    }

    /**
     * test clearCache
     */
    public function test_clearCache()
    {

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

    }


}
