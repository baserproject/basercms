<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainer;
use Cake\Core\Container;

/**
 * Class BcContainerTraitTest
 * @package BaserCore\Test\TestCase\Utility
 */
class BcContainerTest extends BcTestCase
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
     * test Set And Get
     */
    public function testSetAndGet()
    {
        BcContainer::set(new Container());
        $this->assertEquals('Cake\Core\Container', get_class(BcContainer::get()));
    }

}
