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

namespace BcThemeFile\Test\TestCase\Utility;

use App\Application;
use BaserCore\Service\UsersService;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcThemeFile\Utility\BcThemeFileUtil;

/**
 * Class BcThemeFileUtilTest
 * @property BcThemeFileUtil $BcThemeFileUtil
 */
class BcThemeFileUtilTest extends BcTestCase
{
    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test getTemplateTypeName
     * @dataProvider getTemplateTypeNameProvider
     */
    public function test_getTemplateTypeName($type, $expected)
    {
        $rs = BcThemeFileUtil::getTemplateTypeName($type);
        $this->assertEquals($expected, $rs);
    }

    public static function getTemplateTypeNameProvider()
    {

        return [
            ['layout', 'レイアウトテンプレート'],
            ['element', 'エレメントテンプレート'],
            ['email', 'Eメールテンプレート'],
            ['etc', 'コンテンツテンプレート'],
            ['css', 'スタイルシート'],
            ['js', 'Javascript'],
            ['img', 'イメージ'],
            ['other', false],
        ];
    }
}
