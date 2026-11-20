<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBurgerEditor\Test\TestCase\Lib;

use BaserCore\Model\Validation\BcValidation;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBurgerEditor\Lib\BurgerEditorUtil;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Routing\Router;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BurgerEditorUtilTest
 */
class BurgerEditorUtilTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * test getGoogleMapApiKey
     */
    public function test_getGoogleMapApiKey()
    {
        SiteConfigFactory::make(['name' => 'google_maps_api_key', 'value' => 'test-api-key'])->persist();
        $this->assertEquals('test-api-key', BurgerEditorUtil::getGoogleMapApiKey());
    }

    /**
     * test getSuffix
     *
     * enableStaticFileSuffix が無効の場合は空文字を返す
     */
    public function test_getSuffix_disabled()
    {
        Configure::write('Bge.enableStaticFileSuffix', false);
        $path = Plugin::path('BcBurgerEditor') . 'composer.json';
        $this->assertSame('', BurgerEditorUtil::getSuffix($path));
    }

    /**
     * test getSuffix
     *
     * 有効の場合は更新日時のタイムスタンプを返す
     */
    public function test_getSuffix_enabled()
    {
        Configure::write('Bge.enableStaticFileSuffix', true);
        Configure::write('Bge.staticFileSuffix', '');
        $path = Plugin::path('BcBurgerEditor') . 'composer.json';
        $this->assertSame('?' . filemtime($path), BurgerEditorUtil::getSuffix($path));
    }

    /**
     * test getSuffix
     *
     * staticFileSuffix が指定されている場合は末尾に連結する
     */
    public function test_getSuffix_withSuffixText()
    {
        Configure::write('Bge.enableStaticFileSuffix', true);
        Configure::write('Bge.staticFileSuffix', 'v1');
        $path = Plugin::path('BcBurgerEditor') . 'composer.json';
        $this->assertSame('?' . filemtime($path) . '-v1', BurgerEditorUtil::getSuffix($path));
    }

    /**
     * test getAddonPath
     *
     * 標準ではプラグイン直下の Addon のみを返す
     */
    public function test_getAddonPath()
    {
        Configure::write('Bge.enableAddonPlugin', []);
        $result = BurgerEditorUtil::getAddonPath();
        $this->assertCount(1, $result);
        $this->assertSame(Plugin::path('BcBurgerEditor') . 'Addon' . DS, $result[0]);
    }

    /**
     * test getAddonPath
     *
     * 未ロードのプラグインを指定しても無視される
     */
    public function test_getAddonPath_withUnloadedPlugin()
    {
        Configure::write('Bge.enableAddonPlugin', ['BcNotExistsPluginForTest']);
        $this->assertCount(1, BurgerEditorUtil::getAddonPath());
    }


    /**
     * test getDeniedExts
     *
     * 設定した拡張子が小文字化・trim・重複除去されて返る
     */
    public function test_getDeniedExts()
    {
        Configure::write('Bge.deniedExtension', ['PHP', ' phtml ', 'phar', 'php']);
        $this->assertSame(['php', 'phtml', 'phar'], BurgerEditorUtil::getDeniedExts());
    }

    /**
     * test getDeniedExts
     *
     * カンマ区切り文字列でも指定できる
     */
    public function test_getDeniedExts_commaSeparated()
    {
        Configure::write('Bge.deniedExtension', 'php, phtml ,phar');
        $this->assertSame(['php', 'phtml', 'phar'], BurgerEditorUtil::getDeniedExts());
    }

    /**
     * test getDeniedExts
     *
     * 配列でも文字列でもない場合は空配列を返す
     *
     * @dataProvider getDeniedExtsInvalidDataProvider
     */
    public function test_getDeniedExts_invalid($config)
    {
        Configure::write('Bge.deniedExtension', $config);
        $this->assertSame([], BurgerEditorUtil::getDeniedExts());
    }

    public static function getDeniedExtsInvalidDataProvider()
    {
        return [
            [null],
            [''],
            [[]],
            [[null, 123, '']],
        ];
    }

    /**
     * test getDeniedExts
     *
     * 初期設定では php をはじめとする拡張子が含まれる
     */
    public function test_getDeniedExts_default()
    {
        $config = include Plugin::path('BcBurgerEditor') . 'config' . DS . 'setting.php';
        Configure::write('Bge.deniedExtension', $config['Bge']['deniedExtension']);
        $exts = BurgerEditorUtil::getDeniedExts();
        foreach(['php', 'phtml', 'phar', 'cgi', 'htaccess'] as $ext) {
            $this->assertContains($ext, $exts, "{$ext} が初期設定に含まれていません");
        }
    }

    /**
     * test getEditorFieldNames
     *
     * Addon のテンプレートから name 属性を収集する
     */
    public function test_getEditorFieldNames()
    {
        $fields = BurgerEditorUtil::getEditorFieldNames();
        // エディタのテンプレートが出力する入力要素
        $this->assertContains('bge-path', $fields);
        $this->assertContains('bge-alt', $fields);
        // JS が生成する下書き制御用のフィールド
        $this->assertContains('data.Page.contents_tmp', $fields);
        $this->assertContains('data.BlogPost.detail_tmp', $fields);
    }

    /**
     * test getEditorFieldNames
     *
     * 保存対象のフィールドは含めない
     *
     * これらが含まれると FormProtection の保護対象から外れてしまうため、
     * 収集条件を変更する際は必ず確認する。
     */
    public function test_getEditorFieldNames_notContainsModelFields()
    {
        $fields = BurgerEditorUtil::getEditorFieldNames();
        foreach(['content', 'content.id', 'contents', 'draft', '_Token', '_csrfToken'] as $name) {
            $this->assertNotContains($name, $fields, "{$name} が除外対象に含まれています");
        }
    }

    /**
     * test getEditorFieldNames
     *
     * PHP や JS テンプレートの式を含む name は除外する
     */
    public function test_getEditorFieldNames_notContainsExpressions()
    {
        foreach(BurgerEditorUtil::getEditorFieldNames() as $name) {
            $this->assertStringNotContainsString('<', $name);
            $this->assertStringNotContainsString('%', $name);
            $this->assertNotSame('', $name);
        }
    }

    /**
     * test getTypePath
     */
    public function test_getTypePath()
    {
        Configure::write('Bge.enableAddonPlugin', []);
        $expected = Plugin::path('BcBurgerEditor') . 'Addon' . DS . 'type' . DS . 'image' . DS;
        $this->assertSame($expected, BurgerEditorUtil::getTypePath('image'));
        $this->assertFalse(BurgerEditorUtil::getTypePath('not-exists-type'));
    }

    /**
     * test getBlockPath
     */
    public function test_getBlockPath()
    {
        Configure::write('Bge.enableAddonPlugin', []);
        $expected = Plugin::path('BcBurgerEditor') . 'Addon' . DS . 'block' . DS . 'image2' . DS;
        $this->assertSame($expected, BurgerEditorUtil::getBlockPath('image2'));
        $this->assertFalse(BurgerEditorUtil::getBlockPath('not-exists-block'));
    }

    /**
     * test getExtension
     *
     * @param string $filename
     * @param string $expected
     * @dataProvider getExtensionDataProvider
     */
    public function test_getExtension($filename, $expected)
    {
        $this->assertSame($expected, BurgerEditorUtil::getExtension($filename));
    }

    public static function getExtensionDataProvider()
    {
        return [
            ['sample.jpg', 'jpg'],
            ['sample.tar.gz', 'gz'],
            ['/path/to/sample.PNG', 'PNG'],
            // 拡張子がない場合はファイル名がそのまま返る
            ['sample', 'sample'],
        ];
    }

    /**
     * test getFileNameNoExtension
     *
     * @param string $filename
     * @param string $expected
     * @dataProvider getFileNameNoExtensionDataProvider
     */
    public function test_getFileNameNoExtension($filename, $expected)
    {
        $this->assertSame($expected, BurgerEditorUtil::getFileNameNoExtension($filename));
    }

    public static function getFileNameNoExtensionDataProvider()
    {
        return [
            ['sample.jpg', 'sample'],
            ['sample.tar.gz', 'sample.tar'],
            // 拡張子がない場合は空文字となる
            ['sample', ''],
        ];
    }

    /**
     * test mb_basename
     *
     * @param string $path
     * @param string $suffix
     * @param string $expected
     * @dataProvider mbBasenameDataProvider
     */
    public function test_mb_basename($path, $suffix, $expected)
    {
        $this->assertSame($expected, BurgerEditorUtil::mb_basename($path, $suffix));
    }

    public static function mbBasenameDataProvider()
    {
        return [
            ['/var/www/html/日本語ファイル.jpg', '', '日本語ファイル.jpg'],
            ['/var/www/html/日本語ファイル.jpg', '.jpg', '日本語ファイル'],
            // Windows 形式の区切り文字にも対応する
            ['C:\\path\\to\\サンプル.png', '', 'サンプル.png'],
            ['sample.jpg', '', 'sample.jpg'],
        ];
    }

    /**
     * test mb_basename
     *
     * 第2引数を省略しても非推奨警告とならない
     */
    public function test_mb_basename_withoutSuffix()
    {
        $this->assertSame('日本語ファイル.jpg', BurgerEditorUtil::mb_basename('/var/www/html/日本語ファイル.jpg'));
    }

    /**
     * test b64e / b64d
     *
     * エンコードした文字列がデコードで元に戻る
     *
     * @param string $value
     * @dataProvider b64DataProvider
     */
    public function test_b64e_b64d_roundTrip($value)
    {
        $this->assertSame($value, BurgerEditorUtil::b64d(BurgerEditorUtil::b64e($value)));
    }

    public static function b64DataProvider()
    {
        return [
            ['sample'],
            // base64 のパディングが 2 つ付き、連続ドットの変換が発生する
            ['a'],
            // base64 のパディングが 1 つ付く
            ['ab'],
            ['日本語のファイル名'],
            ['記号 + / = を含む'],
            [''],
        ];
    }

    /**
     * test b64e
     *
     * URLで扱えない文字と連続ドットが変換される
     */
    public function test_b64e_notContainsUnsafeCharacters()
    {
        foreach(['sample', 'a', 'ab', '日本語のファイル名'] as $value) {
            $encoded = BurgerEditorUtil::b64e($value);
            $this->assertDoesNotMatchRegularExpression('/[+\/=]/', $encoded, "{$value} のエンコード結果に使用できない文字が含まれています");
            $this->assertStringNotContainsString('..', $encoded, "{$value} のエンコード結果に連続ドットが含まれています");
        }
    }

    /**
     * 埋め込みブロックの HTML を生成する（エディタが保存する形式）
     *
     * @param string $code 埋め込みタグ
     * @return string
     */
    private function embedBlock(string $code): string
    {
        return '<div data-bgb="embed" class="bgb-embed"><div data-bgt="embed" data-bgt-ver="2.1.0" class="bgt-container bgt-embed-container">'
            . '<div class="bge-embed-code" data-bge="embed-code">' . base64_encode($code) . '</div>'
            . '<div class="bge-embed-label" data-bge="embed-label">埋め込みタグ</div>'
            . '</div></div>';
    }

    /**
     * test denyEmbedBlocks
     *
     * 埋め込みブロックがなければ空の配列、あれば false（拒否）を返す
     */
    public function test_denyEmbedBlocks()
    {
        // 埋め込みブロックがない
        $this->assertSame([], BurgerEditorUtil::denyEmbedBlocks('<p>本文</p>'));
        // タグの外（本文の文章）にある embed-code は対象外
        $this->assertSame([], BurgerEditorUtil::denyEmbedBlocks('<p>embed-code という文字列</p>'));

        // 埋め込みブロックがある（中身が無害でも拒否する）
        $this->assertFalse(BurgerEditorUtil::denyEmbedBlocks($this->embedBlock('<iframe src="https://www.youtube.com/embed/xxx"></iframe>')));
        // 属性名の大文字小文字が違う（表示時は復号されないが、判別できないものは拒否する）
        $this->assertFalse(BurgerEditorUtil::denyEmbedBlocks('<div DATA-BGE="embed-code">' . base64_encode('<p>a</p>') . '</div>'));
        // 属性値の中に > がある（正規表現では数え漏れるが、解析で見つかるので拒否する）
        $this->assertFalse(BurgerEditorUtil::denyEmbedBlocks('<div data-bgb="embed"><div data-bgt="embed"><div title=">" data-bge="embed-code">'
            . base64_encode('<img src=x onerror=alert(1)>') . '</div></div></div>'));
        // code 要素の中（解析では中身が取り除かれて見つからず、属性値の > で正規表現も数え漏らす）。
        // 単独では表示時も復号されないが、前の入力欄に <code> を置かれると外に出るため、文字列の並びで拒否する
        $this->assertFalse(BurgerEditorUtil::denyEmbedBlocks('<code title="</code>"><div data-bgb="embed"><div data-bgt="embed"><div title=">" data-bge="embed-code">'
            . base64_encode('<img src=x onerror=alert(1)>') . '</div></div></div></code>'));
    }

    /**
     * test denyEmbedBlocks
     *
     * embed-code という文字列を書かずに、解析後の属性値だけを embed-code にする入力を拒否する
     * （simple_html_dom は属性値の改行を取り除き、解析用の目印を元の文字列に戻す）
     *
     * @dataProvider denyEmbedBlocksAttributeDataProvider
     */
    public function test_denyEmbedBlocks_attribute(string $prefix, string $attribute)
    {
        $html = $prefix . '<div data-bgb="embed"><div data-bgt="embed"><div data-bge="' . $attribute . '">'
            . base64_encode('<img src=x onerror=alert(1)>') . '</div></div></div>';
        $this->assertFalse(BurgerEditorUtil::denyEmbedBlocks($html));
    }

    public static function denyEmbedBlocksAttributeDataProvider(): array
    {
        return [
            '属性値に改行（LF）' => ['', "embed-\ncode"],
            '属性値に改行（CR）' => ['', "embed-\rcode"],
            '属性値に改行（CRLF）' => ['', "embed-\r\ncode"],
            'code 要素を使う目印' => ['<code>code</code>', 'embed-___noise___ 1000'],
            'コメントを使う目印' => ['<!--code-->', 'embed-___noise___ 1000'],
        ];
    }

    /**
     * test denyEmbedBlocks
     *
     * 正規表現が制限（pcre.backtrack_limit）に当たった場合は拒否する
     */
    public function test_denyEmbedBlocks_regexError()
    {
        $jit = ini_get('pcre.jit');
        $limit = ini_get('pcre.backtrack_limit');
        ini_set('pcre.jit', '0');
        ini_set('pcre.backtrack_limit', '1');
        try {
            $this->assertFalse(BurgerEditorUtil::denyEmbedBlocks($this->embedBlock('<p>a</p>')));
        } finally {
            ini_set('pcre.jit', $jit);
            ini_set('pcre.backtrack_limit', $limit);
        }
    }

    /**
     * test containsScript
     *
     * プラグインの設定で denyEmbedBlocks が登録され、管理者以外は埋め込みブロックを保存できないこと
     */
    public function test_containsScript_embedBlock()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        // テスト環境ではプラグインの bootstrap（setting.php の読み込み）が行われないため、設定ファイルを直接読む
        $setting = include Plugin::path('BcBurgerEditor') . 'config' . DS . 'setting.php';
        $this->assertContains(BurgerEditorUtil::class . '::denyEmbedBlocks', $setting['BcApp']['containsScript']['extractors']);

        $extractors = Configure::read('BcApp.containsScript.extractors');
        $allowed = Configure::read('BcApp.allowedPhpOtherThanAdmins');
        Configure::write('BcApp.containsScript.extractors', $setting['BcApp']['containsScript']['extractors']);
        try {
            // 管理者以外：埋め込みブロックは中身にかかわらず拒否、埋め込みブロックのない本文は許可
            $this->assertFalse(BcValidation::containsScript($this->embedBlock('<script>alert(1)</script>')));
            $this->assertFalse(BcValidation::containsScript($this->embedBlock('<iframe src="https://www.youtube.com/embed/xxx"></iframe>')));
            $this->assertTrue(BcValidation::containsScript('<div data-bgb="wysiwyg"><p>本文</p></div>'));

            // 管理者以外にスクリプトを許可する設定：埋め込みブロックも許可
            Configure::write('BcApp.allowedPhpOtherThanAdmins', true);
            $this->assertTrue(BcValidation::containsScript($this->embedBlock('<script>alert(1)</script>')));
            Configure::write('BcApp.allowedPhpOtherThanAdmins', $allowed);

            // 管理者：埋め込みブロックを許可
            Router::setRequest($this->loginAdmin($this->getRequest()));
            $this->assertTrue(BcValidation::containsScript($this->embedBlock('<script>alert(1)</script>')));
        } finally {
            Configure::write('BcApp.containsScript.extractors', $extractors);
            Configure::write('BcApp.allowedPhpOtherThanAdmins', $allowed);
        }
    }

    /**
     * test canUseEmbedBlock
     *
     * 管理者と、管理者以外にスクリプトを許可する設定の場合だけ、埋め込みブロックを使える
     */
    public function test_canUseEmbedBlock()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $allowed = Configure::read('BcApp.allowedPhpOtherThanAdmins');
        try {
            Configure::write('BcApp.allowedPhpOtherThanAdmins', false);
            $this->assertFalse(BurgerEditorUtil::canUseEmbedBlock());
            Configure::write('BcApp.allowedPhpOtherThanAdmins', true);
            $this->assertTrue(BurgerEditorUtil::canUseEmbedBlock());
            Configure::write('BcApp.allowedPhpOtherThanAdmins', false);
            Router::setRequest($this->loginAdmin($this->getRequest()));
            $this->assertTrue(BurgerEditorUtil::canUseEmbedBlock());
        } finally {
            Configure::write('BcApp.allowedPhpOtherThanAdmins', $allowed);
        }
    }

}
