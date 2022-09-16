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

use BaserCore\Error\BcException;
use BaserCore\Service\UtilitiesService;
use BaserCore\TestSuite\BcTestCase;
use Cake\Filesystem\File;

/**
 * Class UtilitiesServiceTest
 * @package BaserCore\Test\TestCase\Service
 * @property UtilitiesService $UtilitiesService
 */
class UtilitiesServiceTest extends BcTestCase
{

    /**
     * ログのパス
     * @var string
     */
    public $logPath = LOGS . 'error.log';

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UtilitiesService = new UtilitiesService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UtilitiesService);
        parent::tearDown();
    }

    /**
     * test deleteLog
     * @return void
     */
    public function testDeleteLog()
    {
        if (!file_exists($this->logPath)) {
            new File($this->logPath, true);
        }

        $this->UtilitiesService->deleteLog();
        $this->assertFalse(file_exists($this->logPath));

        $this->expectExceptionMessage('エラーログが存在しません。');
        $this->expectException(BcException::class);
        $this->UtilitiesService->deleteLog();

    }

}
