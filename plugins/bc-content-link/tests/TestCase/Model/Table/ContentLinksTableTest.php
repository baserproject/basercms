<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcContentLink\Test\TestCase\Model\Table;

use BcContentLink\Model\Table\ContentLinksTable;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class ContentLinksTableTest
 * @property ContentLinksTable $ContentLinks
 */
class ContentLinksTableTest extends BcTestCase
{

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentLinks = $this->getTableLocator()->get('BcContentLink.ContentLinks');

    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentLinks);
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->assertTrue($this->ContentLinks->hasBehavior('BcContents'));
    }

    /**
     * Test validationDefault
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
