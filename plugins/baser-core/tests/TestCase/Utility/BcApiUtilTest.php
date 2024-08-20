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

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcApiUtil;

/**
 * Class BcContainerTraitTest
 */
class BcApiUtilTest extends BcTestCase
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
     * test createAccessToken
     */
    public function testCreateAccessToken()
    {
        $result = BcApiUtil::createAccessToken(1);
        $this->assertArrayHasKey('access_token', $result);
        $this->assertArrayHasKey('refresh_token', $result);
        $this->assertEquals(3, count(explode('.', $result['access_token'])));
    }

    /**
     * test createJwt
     */
    public function testCreateJwt()
    {
        //check if the keys exists then delete them
        $keyPath = CONFIG . 'jwt.key';
        $pemPath = CONFIG . 'jwt.pem';

        if (file_exists($keyPath)) {
            unlink($keyPath);
        }

        if (file_exists($pemPath)) {
            unlink($pemPath);
        }

        //create the keys
        $result = BcApiUtil::createJwt();
        $this->assertTrue($result);

        //check if the keys exists
        $this->assertFileExists($keyPath);
        $this->assertFileExists($pemPath);
    }

}
