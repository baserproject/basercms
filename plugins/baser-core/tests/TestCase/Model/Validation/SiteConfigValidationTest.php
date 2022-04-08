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

namespace BaserCore\Test\TestCase\Model\Validation;

use BaserCore\Model\Validation\SiteConfigValidation;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class SiteConfigValidationTest
 * @package BaserCore\Test\TestCase\Model\Validation
 */
class SiteConfigValidationTest extends BcTestCase
{

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * エイリアスのスラッシュをチェックする
     *
     * - 連続してスラッシュは入力できない
     * - 先頭と末尾にスラッシュは入力できない
     *
     * @param string $alias チェックするエイリアス
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     */
    public function testSslUrlExists()
    {
        $this->assertFalse(SiteConfigValidation::sslUrlExists(true, ['data' => ['admin_ssl' => '']]));
        $this->assertTrue(SiteConfigValidation::sslUrlExists(false, ['data' => ['admin_ssl' => '']]));
    }

}
