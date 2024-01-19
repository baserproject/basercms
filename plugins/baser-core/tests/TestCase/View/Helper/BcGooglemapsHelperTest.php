<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.6
 * @license         https://basercms.net/license/index.html
 */

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\Helper\BcGoogleMapsHelper;
use BaserCore\View\Helper\BcTextHelper;

/**
 * text helper library.
 *
 * @property BcTextHelper $Helper
 * @property BcGoogleMapsHelper $BcGooglemaps
 */
class BcGooglemapsHelperTest extends BcTestCase
{

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
//        $View = new View();
//        $this->BcGooglemaps = new BcGooglemapsHelper($View);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcGooglemaps);
        parent::tearDown();
    }


    /**
     * Google マップ を読み込む
     *
     * @param string $address
     * @param int $width
     * @param int $height
     * @param string $expected 期待値
     * 2018/07/19 ryuring GoogleMapsAPIがAPIキー必須となった為コメントアウト
     * @todo 処理内容を変える等の検討が必要
     */
    public function testLoad()
//	public function testLoad($address, $width, $height, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
//		ob_start();
//		$result = $this->BcGooglemaps->load($address, $width, $height);
//		$output = ob_get_clean();
//
//		if (!empty($address)) {
//			if ($result) {
//				$this->assertMatchesRegularExpression('/' . $expected . '/', $output, 'Google マップを正しく出力できません');
//			} else {
//				$this->markTestIncomplete('GoogleMapの情報の取得に失敗したため、テストをスキップします');
//			}
//
//		} else {
//			$this->assertMatchesRegularExpression('/' . $expected . '/', $output, 'Google マップを正しく出力できません');
//		}

    }

//	public static function loadDataProvider()
//	{
//		return [
//			['福岡', null, null, '<div id="map">'],
//			['福岡', 100, null, '<div id="map" style="width: 100px; height:px">'],
//			['福岡', null, 100, '<div id="map" style="width: px; height:100px">'],
//			['福岡', 100, 100, '<div id="map" style="width: 100px; height:100px">'],
//			['', 100, 100, '^$'],
//		];
//	}

}
