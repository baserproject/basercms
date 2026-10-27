<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.3
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcCustomContent\Test\TestCase\Utility;

use BaserCore\TestSuite\BcTestCase;
use BcCcGoogleMap\Utility\BcCcGoogleMapUtil;

/**
 * BcCcGoogleMapUtilTest
 */
class BcCcGoogleMapUtilTest extends BcTestCase
{

    private const EMBED_URL = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d861.904587167528!2d130.39130656047675!3d33.58989489955554!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x35419187eb655555%3A0x472a05309bec80b9!2z44CSODEwLTAwNDEg56aP5bKh55yM56aP5bKh5biC5Lit5aSu5Yy65aSn5ZCN77yS5LiB55uu77yR77yR4oiS77yS77yVIOaWsOaghOODk-ODqyA26ZqO!5e1!3m2!1sja!2sjp!4v1792990353206!5m2!1sja!2sjp';
    private const EMBED_IFRAME = '<iframe src="' . self::EMBED_URL . '" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>';
    private const FUKUOKA = ['lat' => 33.58989489955554, 'lng' => 130.39130656047675];

    /**
     * test extractUrl
     */
    public function test_extractUrl()
    {
        $this->assertSame(self::EMBED_URL, BcCcGoogleMapUtil::extractUrl(self::EMBED_IFRAME));
        $this->assertSame('https://www.google.com/maps?q=1,2&output=embed', BcCcGoogleMapUtil::extractUrl("<iframe src='https://www.google.com/maps?q=1,2&amp;output=embed'></iframe>"));
        $this->assertSame('35.68,139.76', BcCcGoogleMapUtil::extractUrl(' 35.68,139.76 '));
        $this->assertNull(BcCcGoogleMapUtil::extractUrl(null));
    }

    /**
     * test isGoogleEmbedUrl
     */
    public function test_isGoogleEmbedUrl()
    {
        $this->assertTrue(BcCcGoogleMapUtil::isGoogleEmbedUrl(self::EMBED_URL));
        $this->assertFalse(BcCcGoogleMapUtil::isGoogleEmbedUrl('https://www.google.com/maps/@35.68,139.76,17z'));
        $this->assertFalse(BcCcGoogleMapUtil::isGoogleEmbedUrl('javascript:https://www.google.com/maps/embed?pb=!2d1!3d2'));
        $this->assertFalse(BcCcGoogleMapUtil::isGoogleEmbedUrl(null));
    }

    /**
     * test parse
     * @dataProvider parseDataProvider
     */
    public function test_parse(?string $value, ?array $expected)
    {
        $this->assertSame($expected, BcCcGoogleMapUtil::parse($value));
    }

    public static function parseDataProvider(): array
    {
        $tokyo = ['lat' => 35.681236, 'lng' => 139.767125];
        return [
            'lat,lng' => ['35.681236,139.767125', $tokyo],
            'spaces' => [' 35.681236 , 139.767125 ', $tokyo],
            'negative' => ['-33.8688,151.2093', ['lat' => -33.8688, 'lng' => 151.2093]],
            'integers' => ['35,139', ['lat' => 35.0, 'lng' => 139.0]],
            'google pin priority' => ['https://www.google.com/maps/place/X/@35.6800,139.7600,17z/data=!3m1!4b1!4m6!3m5!1s0x0:0x0!8m2!3d35.681236!4d139.767125', $tokyo],
            'google @' => ['https://www.google.com/maps/@35.681236,139.767125,17z', $tokyo],
            'google q' => ['https://www.google.com/maps?q=35.681236,139.767125', $tokyo],
            'google q plus after comma' => ['https://www.google.com/maps?q=35.681236,+139.767125', $tokyo],
            'google q with z' => ['https://www.google.com/maps?q=35.681236,139.767125&z=10', $tokyo],
            'google @ negative lng' => ['https://www.google.com/maps/@40.7128,-74.006,12z', ['lat' => 40.7128, 'lng' => -74.006]],
            'google query %2C' => ['https://www.google.com/maps/search/?api=1&query=35.681236%2C139.767125', $tokyo],
            'apple ll' => ['https://maps.apple.com/?ll=35.681236,139.767125&q=Tokyo', $tokyo],
            'osm #map' => ['https://www.openstreetmap.org/#map=17/35.681236/139.767125', $tokyo],
            'osm mlat' => ['https://www.openstreetmap.org/?mlat=35.681236&mlon=139.767125', $tokyo],
            'osm marker priority' => ['https://www.openstreetmap.org/?mlat=35.681236&mlon=139.767125#map=17/35.68/139.76', $tokyo],
            'yahoo' => ['https://map.yahoo.co.jp/place?lat=35.681236&lon=139.767125', $tokyo],
            'google embed url' => [self::EMBED_URL, self::FUKUOKA],
            'google embed iframe' => [self::EMBED_IFRAME, self::FUKUOKA],
            'iframe with &amp; in src' => ['<iframe src="https://www.google.com/maps?q=35.681236,139.767125&amp;output=embed"></iframe>', $tokyo],
            'short url 1' => ['https://maps.app.goo.gl/AbCdEf123', null],
            'short url 2' => ['https://goo.gl/maps/AbCdEf123', null],
            'lat out of range' => ['91,139', null],
            'lng out of range' => ['35,181', null],
            'address' => ['東京都千代田区', null],
            'empty' => ['', null],
            'null' => [null, null],
        ];
    }

}
