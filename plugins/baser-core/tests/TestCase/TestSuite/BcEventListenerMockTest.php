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

namespace BaserCore\Test\TestCase\TestSuite;

use BaserCore\TestSuite\BcEventListenerMock;
use BaserCore\TestSuite\BcTestCase;

/**
 * BaserCore\TestSuite\BcTestCase
 *
 */
class BcEventListenerMockTest extends BcTestCase
{
    public function test__constructAndImplementedEvents()
    {
        $bcEventListenerMock = new BcEventListenerMock(['test']);
        $this->assertEquals('test', $bcEventListenerMock->implementedEvents()[0]);
    }
}
