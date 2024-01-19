<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.10
 * @license         https://basercms.net/license/index.html
 */
namespace BaserCore\Test\TestCase\Utility;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcGmaps;

/**
 * Class BcGmapsTest
 *
 * @property BcGmaps $BcGmaps
 */
class BcGmapsTest extends BcTestCase
{
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
     * getInfoLocation
     * 2018/07/09 ryuring TravisCI環境にて、タイミングにより、データを取得できず処理に失敗するので一旦コメントアウト
     */
    public function testGetInfoLocation()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
//		$result = $this->BcGmaps->getInfoLocation('日本');
//		$this->assertNotEmpty($result, 'getInfoLocationで情報を取得できません');
//
//		$lat = round($result['latitude'], 1);
//		$lng = round($result['longitude'], 1);
//
//		$this->assertEquals(36.2, $lat, '位置情報を正しく取得できません');
//		$this->assertEquals(138.3, $lng, '位置情報を正しく取得できません');
//
//		$result = $this->BcGmaps->getInfoLocation('');
//		$this->assertNull($result, 'getInfoLocationに空のアドレスに値が返ってきます');
    }

}
