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

namespace BcThemeConfig\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;

/**
 * BcThemeConfigHelperTest
 */
class BcThemeConfigHelperTest extends BcTestCase
{

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * ロゴを出力する
     * @return void
     */
    public function testLogo()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->expectOutputRegex('/<img src="\/theme\/nada-icons\/img\/logo.png" alt="baserCMS"\/>/');
        $this->BcBaser->logo();
    }

    /**
     * メインイメージを出力する
     * @param array $options 指定するオプション
     * @param string $expect
     * @dataProvider mainImageDataProvider
     */
    public function testMainImage($options, $expect)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->expectOutputRegex('/' . $expect . '/s');
        $this->BcBaser->mainImage($options);
    }

    /**
     * mainImage用のデータプロバイダ
     *
     * このテストは、getThemeImage()のテストも併せて行っています。
     * 1. $optionに指定なし
     * 2. numに指定した番号の画像を表示
     * 3. allをtrue、numに番号を入力し、画像を複数表示
     * 4. 画像にidとclassを付与
     * 5. 画像にpoplinkを付与
     * 6. 画像にaltを付与
     * 7. 画像のlink先を指定
     * 8. 画像にmaxWidth、maxHeightを指定。テストに使う画像は横長なのでwidthが指定される。
     * 9. 画像にwidth、heightを指定。
     * 10. 適当な名前のパラメータを渡す
     * @return array
     */
    public function mainImageDataProvider()
    {
        return [
            [[], '<img src="\/theme\/nada-icons\/img\/main_image_1.jpg" alt="コーポレートサイトにちょうどいい国産CMS"\/>'],
            [['num' => 2], 'main_image_2'],
            [['all' => true, 'num' => 2], '^(.*main_image_1.*main_image_2)'],
            [['all' => true, 'class' => 'test-class', 'id' => 'test-id'], '^(.*id="test-id".*class="test-class")'],
            [['popup' => true], 'href="\/theme\/nada-icons\/img\/main_image_1.jpg"'],
            [['alt' => 'テスト'], 'alt="テスト"'],
            [['link' => '/test'], 'href="\/test"'],
            [['maxWidth' => '200', 'maxHeight' => '200'], 'width="200"'],
            [['width' => '200', 'height' => '200'], '^(.*width="200".*height="200")'],
            [['hoge' => 'hoge'], 'main_image_1'],
        ];
    }

    /**
     * メインイメージの取得でidやclassを指定するオプション
     * @return void
     */
    public function testMainImageIdClass()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $num = 2;
        $idName = 'testIdName';
        $className = 'testClassName';

        //getMainImageを叩いてULを入手(default)
        ob_start();
        $this->BcBaser->mainImage(['all' => true, 'num' => $num]);
        $tags = ob_get_clean();
        $check = preg_match('|<ul id="MainImage">|', $tags) === 1;
        $this->assertTrue($check);

        //getMainImageを叩いてULを入手(id指定)
        ob_start();
        $this->BcBaser->mainImage(['all' => true, 'num' => $num, 'id' => $idName]);
        $tags = ob_get_clean();
        $check = preg_match('|<ul id="' . $idName . '">|', $tags) === 1;
        $this->assertTrue($check);

        //getMainImageを叩いてULを入手(class指定・id非表示)
        ob_start();
        $this->BcBaser->mainImage(['all' => true, 'num' => $num, 'id' => false, 'class' => $className]);
        $tags = ob_get_clean();
        $check = preg_match('|<ul class="' . $className . '">|', $tags) === 1;
        $this->assertTrue($check);
        //getMainImageを叩いてULを入手(全てなし)
        ob_start();
        $this->BcBaser->mainImage(['all' => true, 'num' => $num, 'id' => false, 'class' => false]);
        $tags = ob_get_clean();
        $check = preg_match('|<ul>|', $tags) === 1;
        $this->assertTrue($check);
    }

}
