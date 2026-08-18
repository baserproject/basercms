<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Mcp;

use BaserCore\TestSuite\BcTestCase;
use BcMcp\Mcp\McpContext;

/**
 * McpContextTest
 */
class McpContextTest extends BcTestCase
{

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        McpContext::clear();
        parent::tearDown();
    }

    /**
     * test ログインユーザーIDの設定と取得
     */
    public function testSetAndGetLoginUserId()
    {
        $this->assertNull(McpContext::getLoginUserId());

        McpContext::setLoginUserId(5);
        $this->assertEquals(5, McpContext::getLoginUserId());

        McpContext::clear();
        $this->assertNull(McpContext::getLoginUserId());
    }

}
