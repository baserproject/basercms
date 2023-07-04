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

use BaserCore\Service\UtilitiesService;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcUploader\Model\Table\UploaderFilesTable;
use BcUploader\Service\UploaderConfigsServiceInterface;
use BcUploader\Service\UploaderFilesService;
use BcUploader\Service\UploaderFilesServiceInterface;
use Cake\I18n\FrozenTime;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Composer\Package\Archiver\ZipArchiver;
use Laminas\Diactoros\UploadedFile;

/**
 * UploadFilesServiceTest
 * @property UploaderFilesService $UploaderFilesService
 */
class UploadFilesServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Factory/Permissions',
        'plugin.BaserCore.Factory/PermissionGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/UsersUserGroups',
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UploaderFilesService = $this->getService(UploaderFilesServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test construct
     */
    public function test_construct()
    {
        $this->UploaderFilesService->__construct();
        $this->assertInstanceOf(UploaderFilesTable::class, $this->UploaderFilesService->UploaderFiles);
        $this->assertInstanceOf(UploaderConfigsServiceInterface::class, $this->UploaderFilesService->uploaderConfigsService);

    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {

    }

    /**
     * test createAdminIndexConditions
     */
    public function test_createAdminIndexConditions()
    {

    }

    /**
     * test getControlSource
     */
    public function test_getControlSource()
    {

    }

    /**
     * test get
     */
    public function test_get()
    {

    }

    /**
     * test delete
     */
    public function test_delete()
    {

    }

    /**
     * test create
     */
    public function test_create()
    {
        //準備
        $imgPath = TMP;
        $testFile = $imgPath . 'tmp.png';
        $this->setUploadFileToRequest('backup', $testFile);
        new UploadedFile(
            $testFile,
            100,
            UPLOAD_ERR_OK,
            'tmp.png',
            BcUtil::getContentType($testFile)
        );
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest());
        $postData = [
            'file' => [
                'name' => 'test.png',
                'tmp_name' => 'tmp.png',
                'type' => 'img',
                'size' => 100,
            ],
            'publish_begin' => FrozenTime::yesterday(),
            'publish_end' => FrozenTime::tomorrow(),
        ];
        //正常系実行
        $result = $this->UploaderFilesService->create($postData);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('test.png', $result->file['name']);
        //異常系実行

    }

    /**
     * test update
     */
    public function test_update()
    {

    }

    /**
     * test isEditable
     */
    public function test_isEditable()
    {

    }

    /**
     * test getNew
     */
    public function test_getNew()
    {

    }

}
