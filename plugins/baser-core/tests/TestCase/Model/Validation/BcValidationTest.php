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

use BaserCore\Test\Scenario\InitAppScenario;
use Cake\Routing\Router;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\Validation\BcValidation;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcValidationTest
 * @property BcValidation $BcValidation
 */
class BcValidationTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * Test subject
     *
     * @var BcValidation
     */
    public $BcValidation;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcValidation = new BcValidation();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcValidation);
        Router::reload();
        parent::tearDown();
    }

    /**
     * Test alphaNumericPlus
     *
     * @param string $value
     * @param mixed $option
     * @param boolean $expect
     * @return void
     * @dataProvider alphaNumericPlusDataProvider
     */
    public function testAlphaNumericPlus($value, $option, $expect)
    {
        $result = $this->BcValidation->alphaNumericPlus($value, $option);
        $this->assertEquals($expect, $result);
    }

    public static function alphaNumericPlusDataProvider()
    {
        $alpha = implode('', array_merge(range('a', 'z'), range('A', 'Z')));
        $numeric = implode('', range(0, 9));
        $mark = '-_';
        $allowedChars = $alpha . $numeric . $mark;

        return [
            ['test', [], true],
            ['test!', [], false],
            [$allowedChars, [], true],
            [$allowedChars . '!', [], false],
            ['あいうえお', [], false],
            ['あいうえお', ['あ'], false],
            ['あいうえお', ['あいうえお'], true],
            ['あいうえお_', ['あいうえお'], true],
        ];
    }

    /**
     * Test bcUtileUrlencodeBlank
     *
     * @param string $value
     * @param boolean $expect
     * @return void
     * @dataProvider bcUtileUrlencodeBlankDataProvider
     */
    public function testBcUtileUrlencodeBlank($value, $expect)
    {
        $result = $this->BcValidation->bcUtileUrlencodeBlank($value);
        $this->assertEquals($expect, $result);
    }

    public static function bcUtileUrlencodeBlankDataProvider()
    {
        return [
            ['あいうえお', true],
            ['\\', true],
            ['"', false],
            ["^'|`^(){}[];/?:@&=+$,%<>#!", false]
        ];
    }

    /**
     * Test minLength
     *
     * @param mixed $value
     * @param int $min
     * @param boolean $expect
     * @return void
     * @dataProvider minLengthDataProvider
     */
    public function testMinLength($value, $min, $expect)
    {
        $result = $this->BcValidation->minLength($value, $min);
        $this->assertEquals($expect, $result);
    }

    public static function minLengthDataProvider()
    {
        return [
            ['あいう', 4, false],
            ['あいう', 3, true],
            [['あいう', 'あいうえお'], 4, false],
        ];
    }

    /**
     * Test maxLength
     *
     * @param mixed $value
     * @param int $max
     * @param boolean $expect
     * @return void
     * @dataProvider maxLengthDataProvider
     */
    public function testMaxLength($value, $max, $expect)
    {
        $result = $this->BcValidation->maxLength($value, $max);
        $this->assertEquals($expect, $result);
    }

    public static function maxLengthDataProvider()
    {
        return [
            ['あいう', 4, true],
            ['あいう', 3, true],
            ['あいう', 2, false],
            [['あいう', 'あいうえお'], 4, true],
        ];
    }

    /**
     * Test maxByte
     *
     * @param mixed $value
     * @param int $max
     * @param boolean $expect
     * @return void
     * @dataProvider maxByteDataProvider
     */
    public function testMaxByte($value, $max, $expect)
    {
        $result = $this->BcValidation->maxByte($value, $max);
        $this->assertEquals($expect, $result);
    }

    public static function maxByteDataProvider()
    {
        return [
            ['あいう', 10, true],
            ['あいう', 9, true],
            ['あいう', 8, false]
        ];
    }

    /**
     * Test notInList
     *
     * @param string $value
     * @param array $list
     * @param boolean $expect
     * @return void
     * @dataProvider notInListDataProvider
     */
    public function testNotInList($value, $list, $expect)
    {
        $result = $this->BcValidation->notInList($value, $list);
        $this->assertEquals($expect, $result);
    }

    public static function notInListDataProvider()
    {
        return [
            ['test1', ['test1', 'test2'], false],
            ['test3', ['test1', 'test2'], true],
        ];
    }

    /**
     * Test fileCheck
     *
     * @param string $fileName チェック対象ファイル名
     * @param string $fileSize チェック対象ファイルサイズ
     * @param boolean $expect
     * @dataProvider fileCheckDataProvider
     */
    public function testFileCheck($fileName, $fileSize, $errorCode, $expect)
    {
        $check = [
            "name" => $fileName,
            "size" => $fileSize,
            "error" => $errorCode,
        ];
        $size = 1048576;

        $_POST = ['fileCheck require $_POST' => true];
        $result = $this->BcValidation->fileCheck($check, $size);
        $this->assertEquals($expect, $result);
    }

    public static function fileCheckDataProvider()
    {
        return [
            ["test.jpg", 1048576, 0, true],
            ["", 1048576, 0, true],
            ["test.jpg", null, 2, false],
            [null, null, 4, true],
        ];
    }

    /**
     * ファイルの拡張子チェック
     *
     * @param string $fileName チェック対象ファイル名
     * @param string $fileType チェック対象ファイルタイプ
     * @param boolean $expect
     * @dataProvider fileExtDataProvider
     */
    public function testFileExt($fileType, $expect)
    {
        $ext = "jpg,png";

        $result = $this->BcValidation->fileExt($fileType, $ext);
        $this->assertEquals($expect, $result);
    }

    /**
     * test fileExt - 許可リストとファイル名の拡張子が一致すれば、Content-Type の種類に関係なく許可する
     */
    public function testFileExt_allowedByFileNameExt()
    {
        $file = fn($name, $type) => ['name' => $name, 'size' => 1, 'type' => $type, 'error' => UPLOAD_ERR_OK, 'tmp_name' => 'test'];
        // jpg を許可している場合は JPEG の別の拡張子も許可する
        $this->assertTrue($this->BcValidation->fileExt($file('photo.jpeg', 'image/jpeg'), 'jpg,png'));
        $this->assertTrue($this->BcValidation->fileExt($file('photo.jfif', 'image/jpeg'), 'jpg,png'));
        $this->assertTrue($this->BcValidation->fileExt($file('photo.jpeg', 'image/jpeg'), 'jpeg,png'));
        // Content-Type から導出される拡張子が許可リストに無くても、ファイル名の拡張子で判定する
        $this->assertTrue($this->BcValidation->fileExt($file('figure.eps', 'application/postscript'), 'eps'));
        $this->assertTrue($this->BcValidation->fileExt($file('data.csv', 'text/plain'), 'csv'));
        // ファイル名の拡張子が許可リストに無ければ拒否する
        $this->assertFalse($this->BcValidation->fileExt($file('evil.html', 'image/png'), 'jpg,png'));
        $this->assertFalse($this->BcValidation->fileExt($file('photo.jpeg', 'image/jpeg'), 'png'));
    }

    public static function fileExtDataProvider()
    {
        return [
            [
                [
                    'name' => 'test.jpg',
                    'size' => 1,
                    'type' => 'image/jpeg',
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => 'test',
                    'ext' => 'jpg'
                ],
                true
            ],
            [
                [
                    'name' => 'test.png',
                    'size' => 1,
                    'type' => 'image/jpeg',
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => 'test',
                    'ext' => 'png'
                ],
                true
            ],
            // Content-Type が許可対象でもファイル名の拡張子が許可対象外なら拒否する
            [
                [
                    'name' => 'test.gif',
                    'size' => 1,
                    'type' => 'image/jpeg',
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => 'test',
                    'ext' => 'gif'
                ],
                false
            ],
            // 二重拡張子はファイル名末尾の拡張子で判定する
            [
                [
                    'name' => 'shell.php.x',
                    'size' => 1,
                    'type' => 'image/png',
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => 'test',
                    'ext' => 'png'
                ],
                false
            ],
            [
                [
                    'name' => 'shell.png.php',
                    'size' => 1,
                    'type' => 'image/png',
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => 'test',
                    'ext' => 'png'
                ],
                false
            ],
            // 大文字の拡張子は許可する
            [
                [
                    'name' => 'test.JPG',
                    'size' => 1,
                    'type' => 'image/jpeg',
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => 'test',
                    'ext' => 'jpg'
                ],
                true
            ],
            // 拡張子が無い場合は Content-Type から判定する
            [
                [
                    'name' => 'test',
                    'size' => 1,
                    'type' => 'image/jpeg',
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => 'test',
                    'ext' => 'jpg'
                ],
                true
            ],
            // 拡張子はファイル名で判定するため、Content-Type が異なっても許可リストにあれば許可する
            [
                [
                    'name' => 'test.png',
                    'size' => 1,
                    'type' => 'image/gif',
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => 'test',
                    'ext' => 'png'
                ],
                true
            ]
        ];
    }


    /**
     * Test notFileEmpty
     *
     * @param array $file
     * @param boolean $expect
     * @return void
     * @dataProvider notFileEmptyDataProvider
     */
    public function testNotFileEmpty($file, $expect)
    {
        $result = $this->BcValidation->notFileEmpty($file);
        $this->assertEquals($expect, $result);
    }

    public static function notFileEmptyDataProvider()
    {
        return [
            [['size' => 0], false],
            [['size' => 100], true],
            [[], false],
        ];
    }

    /**
     * Test confirm
     *
     * @param mixed $value
     * @param mixed $fields
     * @param array $data
     * @param boolean $expect
     * @param string $message
     * @return void
     * @dataProvider confirmDataProvider
     */
    public function testConfirm($value, $fields, $data, $expect, $message = null)
    {
        $context = [
            'data' => $data
        ];

        $result = $this->BcValidation->confirm($value, $fields, $context);
        $this->assertEquals($expect, $result, $message);
    }

    public static function confirmDataProvider()
    {
        return [
            ['', ['test1', 'test2'], ['test1' => 'value', 'test2' => 'value'], true, '2つのフィールドが同じ値の場合の判定が正しくありません'],
            ['', ['test1', 'test2'], ['test1' => 'value', 'test2' => 'other_value'], false, '2つのフィールドが異なる値の場合の判定が正しくありません'],
            ['value', 'test', ['test' => 'value'], true, 'フィールド名が一つで同じ値の場合の判定が正しくありません'],
            ['value', 'test', ['test' => 'other_value'], false, 'フィールド名が一つで異なる値の場合の判定が正しくありません'],
        ];
    }

    /**
     * Test emails
     *
     * @param string $value
     * @param boolean $expect
     * @return void
     * @dataProvider emailsDataProvider
     */
    public function testEmails($value, $expect)
    {
        $message = '複数のEメールのバリデーションチェックができません';
        $result = $this->BcValidation->emails($value);
        $this->assertEquals($expect, $result, $message);
    }

    public static function emailsDataProvider()
    {
        return [
            ['test1@co.jp', true],
            ['test1@co.jp,test2@cp.jp', true],
            ['test1@cojp,test2@cp.jp', false],
            ['test1@co.jp,test2@cpjp', false],
        ];
    }


    /**
     * Test notEmptyMultiple
     *
     * @return void
     */
    public function testNotEmptyMultiple()
    {
        $value = [
            '_ids' => [
                'check0' => true,
                'check1' => false
            ],
            'check2' => true,
            'check3' => false
        ];

        $this->assertEquals(false, $this->BcValidation->notEmptyMultiple(['_ids' => true], []));
        $this->assertEquals(false, $this->BcValidation->notEmptyMultiple('value', []));
        $this->assertEquals(true, $this->BcValidation->notEmptyMultiple($value, []));

        unset($value['_ids']['check0']);
        $this->assertEquals(false, $this->BcValidation->notEmptyMultiple($value, []));

        unset($value['_ids']);
        $this->assertEquals(true, $this->BcValidation->notEmptyMultiple($value, []));

        unset($value['check2']);
        $this->assertEquals(false, $this->BcValidation->notEmptyMultiple($value, []));
    }

    /**
     * Test halfText
     *
     * @param string $value
     * @param boolean $expect
     * @return void
     * @dataProvider halfTextDataProvider
     */
    public function testHalfText($value, $expect)
    {
        $result = $this->BcValidation->halfText($value);
        $this->assertEquals($expect, $result);
    }

    public static function halfTextDataProvider()
    {
        return [
            ['test', true],
            ['テスト', false],
        ];
    }

    /**
     * Test checkDateRange
     *
     * @return void
     */
    public function testCheckDateRenge()
    {
        $value = null;
        $context = [
            'data' => [
                'begin' => '2020-01-01 00:00:00',
                'end' => '2021-01-01 00:00:00'
            ]
        ];

        $this->assertEquals(true, $this->BcValidation->checkDateRange($value, ['begin', 'end'], $context));
        $this->assertEquals(true, $this->BcValidation->checkDateRange($value, ['test', 'end'], $context));
        $this->assertEquals(true, $this->BcValidation->checkDateRange($value, ['begin', 'test'], $context));

        $context['data']['end'] = '2020-01-01 00:00:00';
        $this->assertEquals(false, $this->BcValidation->checkDateRange($value, ['begin', 'end'], $context));

        $context['data']['end'] = '2019-01-01 00:00:00';
        $this->assertEquals(false, $this->BcValidation->checkDateRange($value, ['begin', 'end'], $context));
    }

    /**
     * Test checkDateAfterThan
     *
     * @param string $value
     * @param string $target
     * @param boolean $expect
     * @return void
     * @dataProvider checkDataAfterThanDataProvider
     */
    public function testCheckDateAfterThan($value, $target, $expect)
    {
        $context = [
            'data' => [
                'target' => $target
            ]
        ];

        $result = $this->BcValidation->checkDateAfterThan($value, 'target', $context);
        $this->assertEquals($expect, $result);
    }

    public static function checkDataAfterThanDataProvider()
    {
        return [
            ['2015-01-01 00:00:00', '2015-01-01 00:00:00', false],
            ['2015-01-01 24:00:01', '2015-01-02 00:00:00', true],
            ['2015-01-01 00:00:00', '2015-01-02 00:00:00', false],
            ['2015-01-02 00:00:00', '2015-01-01 00:00:00', true],
        ];
    }

    /**
     * Test containsScript
     *
     * @return void
     * @dataProvider containsScriptDataProvider
     */
    public function testContainsScript($value, $expect, $admin = false)
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        if ($admin) Router::setRequest($this->loginAdmin($this->getRequest()));
        $result = $this->BcValidation->containsScript($value);
        $this->assertEquals($expect, $result);
    }

    public static function containsScriptDataProvider()
    {
        return [
            // phpコードの場合
            ['<?php echo $test; ?>', false],
            // jsコードの場合
            ['<script type="text/javascript">', false],
            // イベントが入ってる場合
            ['<input type="text" onclick="select()"/>', false],
            // jsコードへのリンクがある場合
            ['<a href="javascript:doSomething();">click me</a>', false],
            // アドミンユーザーでログインしてる場合
            ['<a href="javascript:doSomething();">click me</a>', true, true],

            ['<iframe src="javascript:alert(origin)">', false],
            ['<a href="&#x6a;avascript:alert(origin)">click me</a> ', false],
            ['<iframe srcdoc="&lt;script&gt;alert(origin)&lt;/script&gt;">', false],
            ['<form action="javascript:alert(1)"><input type="submit"></form>', false],
            ['<style>@keyframes slidein {}</style><xss style="animation-duration:1s;animation-name:slidein;animation-iteration-count:2" onwebkitanimationiteration="alert(origin)"></xss>', false],
            ['<img srcset="images/small.jpg 320w, images/medium.jpg 640w, images/large.jpg 1280w" src="images/large.jpg" sizes="(max-width:1280px) 100vw, 1280px">', true],
            ['<meta http-equiv="refresh" content="0; url=http://;url=javascript:alert(\'xss\');">', false],
            ['<iframe src="javascript:alert(\'xss\');"></iframe>', false],
            ['<iframe src="https://example.com"></iframe>', true],
            ['<a href="jav&#x09;ascript:alert(\'XSS\');">link</a>', false],
            ['<a href="jav&#x0A;ascript:alert(\'XSS\');">link</a>', false],
            ['<a href="jav&#x0D;ascript:alert(\'XSS\');">link</a>', false],
            ['<object data=# codebase="javascript:alert(origin)//">', false],
            ['<object data="javascript:alert(1)">', false],
            ['<button formaction="javascript:alert(1)">click</button>', false],
            ['<object data="https://example.com/movie.mp4" type="video/mp4"></object>', true],

            // --- JVN#76990966 対策: 難読化されたスクリプトを拒否する ---
            // パターン1: HTML5実体参照でjavascript:スキームを難読化
            ['<a href="java&Tab;script:alert(origin)">test</a>', false],
            ['<a href="javascript&colon;alert(origin)">x</a>', false],
            ['<a href="javas&NewLine;cript:alert(origin)">x</a>', false],
            // パターン2: コメント偽装・セミコロン無し数値文字参照
            ["&lt;!--\n<svg id=\">\" onload=alert(origin)></svg>\n--&gt;", false],
            ['<a href="j&#x61vascript:alert(origin)">x</a>', false],
            ['<a href="j&#97vascript:alert(origin)">x</a>', false],
            // パターン3: 構文的に未完結なタグ（結合攻撃の前半を拒否）
            ["<input type=hidden value='", false],
            // パターン4: ブラックリスト外の属性/要素
            ['<object data=# codebase=java&Tab;script:alert(origin)//></object>', false],

            // --- 無害なリッチHTMLは保存を許可する（誤検知しない） ---
            ['<figure><img src="/a.png" alt=""><figcaption>キャプション</figcaption></figure>', true],
            ['<details open><summary>概要</summary>詳細</details>', true],
            ['<p style="color:red;text-align:center">装飾テキスト</p>', true],
            ['<span data-id="1" data-role="note">データ属性</span>', true],
            ['<table class="tbl"><tr><td>セル</td></tr></table>', true],
            ['<a href="mailto:info@example.com">メール</a>', true],
            ['<a href="/news/archives/1">次の記事</a>', true],

            // --- HTML5 / WAI-ARIA の無害な記述は保存を許可する（#4633） ---
            ['<section><header><h2>見出し</h2></header><article><p>本文</p></article><footer>補足</footer></section><nav>ナビ</nav><aside>補足</aside><main>メイン</main>', true],
            ['<picture><source srcset="/a.webp 1x, /a@2x.webp 2x" media="(min-width:600px)" type="image/webp"><img src="/a.jpg" alt=""></picture>', true],
            ['<p><time datetime="2026-11-09">11月9日</time><mark>強調</mark></p>', true],
            ['<img src="/a.jpg" alt="" fetchpriority="high" loading="lazy" decoding="async">', true],
            ['<video src="/a.mp4" controls autoplay muted loop playsinline preload="none"></video><audio src="/a.mp3" controls preload="metadata"></audio>', true],
            ['<p><del datetime="2026-11-09">旧</del><ins datetime="2026-11-09">新</ins></p>', true],
            ['<div role="note" aria-label="注記">注記</div><a href="/" aria-current="page">トップ</a>', true],
            ['<a href="https://example.com/" rel="sponsored">広告</a>', true],
            // YouTube の埋め込みコード
            ['<iframe width="560" height="315" src="https://www.youtube.com/embed/xxxx" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>', true],
            // Google マップの埋め込みコード
            ['<iframe src="https://www.google.com/maps/embed?pb=xxxx" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>', true],
            // BurgerEditor の画像・画像リンク・トリミング画像・ダウンロードブロックの出力
            ['<div data-bgt="image" data-bgt-ver="2.24.0" class="bgt-container bgt-image-container"><div class="bgt-image" data-bge-popup="1" data-bge-empty="0" data-bge-hr="" data-bge="popup:data-bge-popup, empty:data-bge-empty, hr:data-bge-hr"><a class="bgt-image__link" href="/files/bgeditor/a.png" data-bge="path:href"><figure class="bgt-link__box"><div class="bgt-box__image-container"><img src="/files/bgeditor/a.png" data-bge="path:src, srcset:srcset, alt:alt, width:width, height:height, loading:loading, decoding:decoding" srcset="/files/bgeditor/a.png, /files/bgeditor/a__org.png 2x" alt="" width="800" height="600" loading="lazy" decoding="async"></div><figcaption class="bgt-box__caption" data-bge="caption"></figcaption></figure></a></div></div>', true],
            ['<a class="bgt-image-link__link" href="https://example.com/" target="_blank" rel="noopener noreferrer"><img src="/files/bgeditor/a.png" srcset="" alt="" width="auto" height="auto" loading="eager" decoding="auto"></a>', true],
            ['<div class="bgt-box__image" data-bge="path:style(background-image), alt" style="background-image: url(/files/bgeditor/%E7%94%BB%E5%83%8F.png)">代替テキスト</div>', true],
            ['<a class="bgt-download-file__link" href="/files/bgeditor/a.pdf" target="_blank" data-bge="path:href, download:download" download="資料.pdf"><i class="bgt-link__icon bgt-link__icon--before" role="presentation"></i><span class="bgt-link__name" data-bge="name">資料</span></a>', true],
            // CKEditor の表・画像回り込み
            ['<table style="width:100%;border-collapse:collapse" border="1"><tr><td style="width:50%;padding:5px;border-bottom:1px solid #ccc">セル</td></tr></table>', true],
            ['<img src="/a.png" alt="" style="float:right;margin:10px;width:300px;height:200px">', true],

            // --- HTML5 の記述でも危険なものは拒否する ---
            ['<section onclick="alert(origin)">a</section>', false],
            ['<video src="/a.mp4" onplay="alert(origin)"></video>', false],
            ['<a href="/" aria-label="x" onmouseover="alert(origin)">a</a>', false],
            ['<div style="background-image:url(javascript:alert(origin))">a</div>', false],
            ['<picture><source srcset="/a.webp 1x, javascript:alert(origin) 2x"><img src="/a.jpg" alt=""></picture>', false],
            ['<img src="/a.jpg" alt="" srcset="jav&#x61;script:alert(origin)">', false],
            // iframe の allow で、カメラ・マイク等の権限を埋め込み先に渡す
            ['<iframe src="https://example.com/" allow="camera; microphone"></iframe>', false],
            ['<iframe src="https://example.com/" allow="autoplay; geolocation"></iframe>', false],
            // 閲覧中ページの URL をそのまま埋め込み先に渡す
            ['<iframe src="https://example.com/" referrerpolicy="unsafe-url"></iframe>', false],
            // 許可外の CSS プロパティは、許可されたプロパティと併記しても拒否する（全面オーバーレイ等）
            ['<a href="https://example.com/" style="color:red;position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999">x</a>', false],
            ['<a href="/" style="color:red;opacity:0">x</a>', false],
        ];
    }

    /**
     * Test checkKatakana
     *
     * @return void
     */
    public function testCheckKatakana()
    {
        $t = [
            'アい' => false,
            'ァアイウエオラヰヱヴヸヹヺ・ーヽヾ' => true,
            ' 　' => true,
            'ゞカタカナ' => false,
            'English' => false,
            '漢字' => false,
        ];
        foreach($t as $str => $hope) {
            $result = $this->BcValidation->checkKatakana($str);
            $this->assertEquals($result, $hope);
        }

        $result = $this->BcValidation->checkKatakana('カタカナ除外カタカナ', '除外');
        $this->assertEquals($result, true);

        $result = $this->BcValidation->checkKatakana('スペースキンシ　 ', '');
        $this->assertEquals($result, false);
    }

    /**
     * Test checkHiragana
     *
     * @return void
     */
    public function testCheckHiragana()
    {
        $t = [
            'アい' => false,
            'ぁあぃいぅうぇえぉおかがきぎくぐけげこごさざしじすずせぜそぞただちぢっつづてでとどなにぬねのはばぱひびぴふぶぷへべぺほぼぽまみむめもゃやゅゆょよらりるれろゎわゐゑをんゔゕゖ' => true,
            ' 　ー' => true,
            'ヾカタカナ' => false,
            'English' => false,
            '漢字' => false,
        ];
        foreach($t as $str => $hope) {
            $result = $this->BcValidation->checkHiragana($str);
            $this->assertEquals($result, $hope);
        }

        $result = $this->BcValidation->checkHiragana('ひらがな除外ひらがな', '除外');
        $this->assertEquals($result, true);

        $result = $this->BcValidation->checkHiragana('すぺーすきんし　 ', '');
        $this->assertEquals($result, false);
    }

    /**
     * test checkSelectList
     */
    public function test_checkSelectList()
    {
        //戻り＝falseケース
        $str = "あ\rべ\r\nあ\nべ\ntest";
        $result = $this->BcValidation->checkSelectList($str);
        $this->assertFalse($result);
        //戻り＝trueケース
        $str = "あa\nべ\nあ";
        $result = $this->BcValidation->checkSelectList($str);
        $this->assertTrue($result);
    }

    /**
     * 範囲を指定しての長さチェック
     *
     * @param mixed $check
     * @param int $min
     * @param int $max
     * @param boolean $expect
     * @dataProvider betweenDataProvider
     */
    public function testBetween($check, $min, $max, $expect)
    {
        $result = $this->BcValidation->between($check, $min, $max);
        $this->assertEquals($expect, $result);
    }

    public static function betweenDataProvider()
    {
        return [
            ["あいう", 2, 4, true],
            ["あいう", 3, 3, true],
            ["あいう", 4, 3, false],
            ["あいうえお", 2, 4, false],
        ];
    }


    /**
     * test notBlankOnlyString
     */
    public function test_notBlankOnlyString()
    {
        //戻り＝falseケース：半角スペース
        $str = " ";
        $result = $this->BcValidation->notBlankOnlyString($str);
        $this->assertFalse($result);
        //戻り＝falseケース：全角スペース
        $str = "　";
        $result = $this->BcValidation->notBlankOnlyString($str);
        $this->assertFalse($result);
        //戻り＝falseケース：半角・全角
        $str = "　 ";
        $result = $this->BcValidation->notBlankOnlyString($str);
        $this->assertFalse($result);
        //戻り＝trueケース
        $str = "あa　";
        $result = $this->BcValidation->notBlankOnlyString($str);
        $this->assertTrue($result);
    }

    /**
     * @param $str
     * @param $key
     * @param $regex
     * @param $expect
     *
     * test checkWithJson
     * @dataProvider checkWithJsonProvider
     *
     */
    public function test_checkWithJson($key, $str, $regex, $expect)
    {
        $request = $this->getRequest('/')->withData('validate', ['EMAIL_CONFIRM', 'FILE_EXT', 'MAX_FILE_SIZE']);
        Router::setRequest($request);

        $result = $this->BcValidation->checkWithJson($str, $key, $regex);
        $this->assertEquals($expect, $result);
    }

    public static function checkWithJsonProvider()
    {
        return [
            //Eメール比較先フィールド名 テスト
            ['BcCustomContent.email_confirm', '{"BcCustomContent":{"email_confirm":"ああ"}}', "/^[a-z0-9_]+$/", false],       //戻り＝falseケース：全角文字
            ['BcCustomContent.email_confirm', '{"BcCustomContent":{"email_confirm":" "}}', "/^[a-z0-9_]+$/", false],         //戻り＝falseケース：半角スペース
            ['BcCustomContent.email_confirm', '{"BcCustomContent":{"email_confirm":"ああaaa"}}', "/^[a-z0-9_]+$/", false],    //戻り＝falseケース：半角・全角
            ['BcCustomContent.email_confirm', '{"BcCustomContent":{"email_confirm":"aaaa_bbb"}}', "/^[a-z0-9_]+$/", true],    //戻り＝trueケース

            //ファイルアップロードサイズ制限 テスト
            ['BcCustomContent.max_file_size', '{"BcCustomContent":{"max_file_size":"aaaaa111"}}', "/^[0-9]+$/", false],       //戻り＝falseケース：文字列
            ['BcCustomContent.max_file_size', '{"BcCustomContent":{"max_file_size":"ああaaa"}}', "/^[0-9]+$/", false],         //戻り＝falseケース：半角・全角
            ['BcCustomContent.max_file_size', '{"BcCustomContent":{"max_file_size":"123"}}', "/^[0-9]+$/", true],             //戻り＝falseケース：数値

            //ファイル拡張子チェック テスト
            ['BcCustomContent.file_ext', '{"BcCustomContent":{"file_ext":"ああaaa"}}', "/^[a-z,]+$/", false],                  //戻り＝falseケース：半角・全角
            ['BcCustomContent.file_ext', '{"BcCustomContent":{"file_ext":"1111"}}', "/^[a-z,]+$/", false],                    //戻り＝falseケース：数字
            ['BcCustomContent.file_ext', '{"BcCustomContent":{"file_ext":"pdf,png"}}', "/^[a-z,]+$/", true],                  //戻り＝trueケース：文字列
        ];
    }

    /**
     * test hexColorPlus
     *
     * @param string $value
     * @param boolean $expect
     * @return void
     * @dataProvider hexColorPlusDataProvider
     */
    public function testHexColorPlus($value, $expect)
    {
        $result = $this->BcValidation->hexColorPlus($value);
        $this->assertEquals($expect, $result);
    }

    public static function hexColorPlusDataProvider()
    {
        return [
            ['000', true],
            ['0fF', true],
            ['123f', true],
            ['123abc', true],
            ['1234abcd', true],
            ['black', false],
            ['#000', false]
        ];
    }

    /**
     * @dataProvider reservedDataProvider
     */
    public function test_reserved($value, $expect)
    {
        $result = $this->BcValidation->reserved($value);
        $this->assertEquals($expect, $result);
    }

    public static function reservedDataProvider()
    {
        return [
            ['test', true],
            ['admin', true],
            ['accessible', false],
            ['before', false],
        ];
    }

    /**
     * test notContainsShortCode
     * @dataProvider notContainsShortCodeDataProvider
     */
    public function test_notContainsShortCode($value, $expect)
    {
        $this->assertEquals($expect, $this->BcValidation->notContainsShortCode($value));
    }

    public static function notContainsShortCodeDataProvider()
    {
        return [
            ['', true],
            [null, true],
            ['普通のコメントです。[注] も書けます。', true],
            ['[BcBaser.getSitemapList]', true],
            ['[BcBaser.getSitemap]', false],
            ['前置き [BcBaser.getGoogleMaps apiKey|k] 後ろ', false],
            ["[Mail.getForm\n1]", false],
        ];
    }

}
