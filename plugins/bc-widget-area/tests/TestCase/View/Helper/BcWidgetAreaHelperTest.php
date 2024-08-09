<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcWidgetArea\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcFrontAppView;
use BaserCore\View\Helper\BcTextHelper;
use BcWidgetArea\View\Helper\BcWidgetAreaHelper;
use Cake\View\View;

/**
 * text helper library.
 *
 * @property BcTextHelper $Helper
 * @property BcWidgetAreaHelper $BcWidgetArea
 */
class BcWidgetAreaHelperTest extends BcTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $View = new View();
        $this->BcWidgetArea = new BcWidgetAreaHelper($View);
    }

    public function tearDown(): void
    {
        unset($this->BcWidgetArea);
        parent::tearDown();
    }

    /**
     * ウィジェットエリアを表示する
     *
     * @param $no ウィジェットエリアNO
     * @param array $options オプション
     * @param string $expected 期待値
     * @dataProvider showDataProvider
     *
     * MEMO: $pathがわからないため保留
     */
    public function testShow($fileName, $no, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $path = APP . 'Elements/widgets/' . $fileName . '.ctp';
        $fh = fopen($path, 'w');
        fwrite($fh, '東京' . PHP_EOL . '埼玉' . PHP_EOL . '大阪' . PHP_EOL);
        fclose($fh);

        ob_start();
        //エラーでファイルが残留するため,tryで確実に削除を実行
        try {
            $this->BcWidgetArea->show($no);
        } catch (Exception $e) {
            echo 'error: ', $e->getMessage(), "\n";
        }
        $result = ob_get_clean();
        unlink($path);

        pr($result);
        $this->assertEquals($expected, $result);
    }

    public static function showDataProvider()
    {
        return [
            ['test', 1, ''],
            ['test', 2, '']
        ];
    }

    /**
     * ウィジェットエリアを出力する
     *
     * $noを指定していない場合、ウィジェットが出力されません。
     *
     * @param string $url 現在のURL
     * @param int $no
     * @param string $expected 期待値
     * @dataProvider getWidgetAreaDataProvider
     * @TODO: $noが指定されてない(null)場合のテストを記述する
     */
    public function testGetWidgetArea($url, $no, $expected)
    {
        $view = new BcFrontAppView($this->getRequest($url));
        $view->set('currentWidgetAreaId', 1);
        $this->BcWidgetArea = new BcWidgetAreaHelper($view);
        $this->assertMatchesRegularExpression('/' . $expected . '/', $this->BcWidgetArea->getWidgetArea($no));
    }

    public static function getWidgetAreaDataProvider()
    {
        return [
            ['/company', 1, '<div class="bs-widget-area bs-widget-area-1">'],
            ['/company', 2, '<div class="bs-widget-area bs-widget-area-2">'],
            ['/company', null, '<div class="bs-widget-area bs-widget-area-1">'],
        ];
    }

}
