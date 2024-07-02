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

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\TestSuite\BcTestCase;

/**
 * Class TwoFactorAuthenticationsTableTest
 */
class TwoFactorAuthenticationsTableTest extends BcTestCase
{
    /**
     * @var TwoFactorAuthenticationsTable
     */
    public $TwoFactorAuthentications;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->TwoFactorAuthentications = $this->getTableLocator()->get('BaserCore.TwoFactorAuthentications');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->TwoFactorAuthentications);
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertEquals('two_factor_authentications', $this->TwoFactorAuthentications->getTable());
        $this->assertTrue($this->TwoFactorAuthentications->hasBehavior('Timestamp'));
    }

}
