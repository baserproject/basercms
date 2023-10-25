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

namespace BaserCore\Test\TestCase\Middleware;

use BaserCore\Middleware\BcRedirectMainSiteMiddleware;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class BcRedirectMainSiteFilterTest
 *
 * @property  BcRedirectMainSiteMiddleware $BcRedirectMainSiteMiddleware
 */
class BcRedirectMainSiteFilterTest extends BcTestCase
{

    /**
     * set up
     *
     * @return void
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
     * beforeDispatch Event
     */
    public function testBeforeDispatch()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
