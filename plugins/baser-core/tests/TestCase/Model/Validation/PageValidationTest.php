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

use BaserCore\Model\Validation\PageValidation;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class PageValidationTest
 * @property PageValidation $PageValidation
 */
class PageValidationTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var PageValidation
     */
    public $PageValidation;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PageValidation = new PageValidation();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PageValidation);
        parent::tearDown();
    }


    /**
     * PHP構文チェック
     * 成功時
     *
     * @param string $code PHPのコード
     * @return void
     * @dataProvider phpValidSyntaxDataProvider
     */
    public function testPhpValidSyntax($code)
    {
        $this->assertTrue($this->PageValidation->phpValidSyntax($code));
    }

    public static function phpValidSyntaxDataProvider()
    {
        return [
            [''],
            ['<?php $this->BcBaser->setTitle(\'test\');'],
            // 本文が大きい場合でも exec() がシェルの引数長の上限（Linuxの場合 MAX_ARG_STRLEN、
            // 通常128KiB）で fork に失敗し、構文エラーと誤判定しないことを確認する
            [str_repeat('a', 200000)],
        ];
    }

    /**
     * PHP構文チェック
     * 失敗時
     *
     * @param string $line エラーが起こる行
     * @param string $code PHPコード
     * @return void
     * @dataProvider phpValidSyntaxWithInvalidDataProvider
     */
    public function testPhpValidSyntaxWithInvalid($line, $code)
    {
        $this->assertStringContainsString("on line {$line}", $this->PageValidation->phpValidSyntax($code));
    }

    public static function phpValidSyntaxWithInvalidDataProvider()
    {
        return [
            [1, '<?php echo \'test'],
            [2, '<?php echo \'test\';' . PHP_EOL . 'echo \'hoge']
        ];
    }
}
