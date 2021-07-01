<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Validation;

use BaserCore\Model\Validation\SiteValidation;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\AppTable;

/**
 * Class SiteValidationTest
 * @package BaserCore\Test\TestCase\Model\Validation
 * @property SiteValidation $SiteValidation
 */
class SiteValidationTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var SiteValidation
     */
    public $SiteValidation;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SiteValidation = new SiteValidation();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SiteValidation);
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
     * @dataProvider checkUrlDataProvider
     */
    public function testAliasSlashChecks($alias, $expected, $message = null)
    {
        $result = $this->SiteValidation->aliasSlashChecks($alias);
        $this->assertEquals($expected, $result);
    }
    public function checkUrlDataProvider()
    {
        return [
            ['en', true],
            ['hoge//', false],
            ['/hoge', false],
            ['ho/ge', true],
        ];
    }
}
