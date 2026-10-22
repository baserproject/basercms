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

    /**
     * test normalizePath
     * @dataProvider normalizePathProvider
     */
    public function test_normalizePath($path, $expected)
    {
        $this->assertEquals($expected, BcThemeFileUtil::normalizePath($path));
    }

    public static function normalizePathProvider()
    {
        return [
            ['/var/www/html/plugins/BcColumn/webroot/css/style.css', '/var/www/html/plugins/BcColumn/webroot/css/style.css'],
            ['/var/www/html/plugins/BcColumn/webroot/css/../../../bc-front/webroot/css/style.css', '/var/www/html/plugins/bc-front/webroot/css/style.css'],
            ['/var/www/html/plugins/BcColumn//webroot/./css/', '/var/www/html/plugins/BcColumn/webroot/css'],
            ['/../../etc/passwd', '/etc/passwd'],
        ];
    }

    /**
     * test isWithinDir
     * @dataProvider isWithinDirProvider
     */
    public function test_isWithinDir($path, $baseDir, $expected)
    {
        $this->assertEquals($expected, BcThemeFileUtil::isWithinDir($path, $baseDir));
    }

    public static function isWithinDirProvider()
    {
        $base = '/var/www/html/plugins/BcColumn/webroot/img/';
        return [
            '配下の実在ファイル' => [$base . 'logo.png', $base, true],
            '配下の未作成ファイル' => [$base . 'new_dir/new.png', $base, true],
            '基準ディレクトリ自身' => [$base, $base, true],
            '基準ディレクトリ自身（末尾スラッシュ無し）' => [rtrim($base, '/'), $base, true],
            '別テーマへの traversal（実在）' => [$base . '../../../bc-front/webroot/img/logo.png', $base, false],
            'config への traversal（未作成）' => [$base . '../../../../config/pwned.png', $base, false],
            '前方一致だけ同じ別ディレクトリ' => ['/var/www/html/plugins/BcColumn/webroot/img_evil/x.png', $base, false],
        ];
    }
}
