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

namespace BcSearchIndex\Test\TestCase\Form;

use BcSearchIndex\Form\SearchIndexesFrontForm;
use BaserCore\TestSuite\BcTestCase;
use Cake\Form\Schema;

/**
 * Class SearchIndexesFrontFormTest
 */
class SearchIndexesFrontFormTest extends BcTestCase
{

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test _buildSchema
     *
     * @return void
     */
    public function test_buildSchema(): void
    {
        $schema = $this->execPrivateMethod(new SearchIndexesFrontForm(), '_buildSchema', [new Schema()]);
        $this->assertEquals('string', $schema->fieldType('f'));
        $this->assertEquals('string', $schema->fieldType('q'));
        $this->assertEquals('string', $schema->fieldType('s'));
    }

}
