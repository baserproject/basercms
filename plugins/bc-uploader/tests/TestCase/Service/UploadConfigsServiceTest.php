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

/**
 * UploadConfigsServiceTest
 * @property UploaderConfigsService $UploaderConfigsService
 *
 */
class UploadConfigsServiceTest extends BcTestCase
{

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

    }


}
