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

use BaserCore\Model\Validation\PermissionValidation;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\AppTable;

/**
 * Class PermissionValidationTest
 * @package BaserCore\Test\TestCase\Model\Validation
 * @property PermissionValidation $PermissionValidation
 */
class PermissionValidationTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var PermissionValidation
     */
    public $PermissionValidation;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PermissionValidation = new PermissionValidation();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PermissionValidation);
        parent::tearDown();
    }

    /**
     * 設定をチェックする
     *
     * @param string $check チェックするURL
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider checkUrlDataProvider
     */
    public function testCheckUrl($url, $expected, $message = null)
    {
        $result = $this->PermissionValidation->checkUrl($url);
        $this->assertEquals($expected, $result, $message);
    }

    public function checkUrlDataProvider()
    {
        return [
            ['1', false, '適当なURLです'],
            ['hoge', false, '適当なURLです'],
            ['/hoge', false, '適当なURLです'],
            ['hoge/', false, '適当なURLです'],
            ['/hoge/', false, '適当なURLです'],
            ['/hoge/*', false, '適当なURLです'],
            ['baser/admin', true, '権限の必要なURLです'],
            ['/baser/admin', true, '権限の必要なURLです'],
            ['baser/admin/', true, '権限の必要なURLです'],
            ['baser/admin/*', true, '権限の必要なURLです'],
            ['/baser/admin/*', true, '権限の必要なURLです'],
            ['/baser/admin/dashboard/', true, '権限の必要なURLです'],
            ['/baser/admin/dashboard/*', true, '権限の必要なURLです'],
        ];
    }
}
