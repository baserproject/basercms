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

namespace BcUploader\Test\TestCase\Model\Table;

use BaserCore\TestSuite\BcTestCase;
use BcUploader\Model\Table\UploaderConfigsTable;

/**
 * Class UploaderConfigsTableTest
 *
 * @property  UploaderConfigsTable $UploaderConfigsTable
 */
class UploaderConfigsTableTest extends BcTestCase
{
    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UploaderConfigsTable = $this->getTableLocator()->get('BcUploader.UploaderConfigs');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UploaderConfigsTable);
        parent::tearDown();
    }

    /**
     * initialize
     */
    public function test_initialize()
    {
        $this->assertEquals('uploader_configs', $this->UploaderConfigsTable->getTable());
        $this->assertTrue($this->UploaderConfigsTable->hasBehavior('BcKeyValue'));
    }
}
