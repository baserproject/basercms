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
use A\B;
use BaserCore\Error\BcException;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcGmaps;
use Cake\Cache\Cache;

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
        $this->BcGmaps = new BcGmaps('api-key');
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
     * test __construct
     */
    public function test__construct()
    {
        $_gmapsApiUrl = $this->getPrivateProperty($this->BcGmaps, '_gmapsApiUrl');
        $this->assertEquals('https://maps.googleapis.com/maps/api/geocode/xml?key=api-key', $_gmapsApiUrl);

        $this->expectException(BcException::class);
        $this->expectExceptionMessage('システム基本設定にて、Google Maps API キーを入力してください。');
        new BcGmaps(false);
    }

    /**
     * test getLocation
     */
    public function testGetLocation()
    {
        Cache::write('5pel5pys', '33.0661504,126.551622,5z', '_bc_gmaps_');
        $result = $this->BcGmaps->getLocation('日本');
        $this->assertEquals('33.0661504,126.551622,5z', $result);

        $result = $this->BcGmaps->getLocation('');
        $this->assertNull($result, 'getLocationに空のアドレスに値が返ってきます');

        Cache::delete('5pel5pys', '_bc_gmaps_');
    }

}
