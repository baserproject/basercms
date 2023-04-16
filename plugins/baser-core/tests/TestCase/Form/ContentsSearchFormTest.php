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

namespace BaserCore\Test\TestCase\Form;

use BaserCore\Form\ContentsSearchForm;
use BaserCore\TestSuite\BcTestCase;
use Cake\Form\Schema;

/**
 * Class ContentsSearchFormTest
 */
class ContentsSearchFormTest extends BcTestCase
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
     * test _buildSchema
     */
    public function test_buildSchema()
    {
        $ContentsSearchForm = new ContentsSearchForm();
        $result = $this->execPrivateMethod($ContentsSearchForm, '_buildSchema', [new Schema()]);
        $this->assertEquals('string', $result->fieldType('folder_id'));
        $this->assertEquals('string', $result->fieldType('name'));
        $this->assertEquals('string', $result->fieldType('type'));
        $this->assertEquals('string', $result->fieldType('self_status'));
        $this->assertEquals('string', $result->fieldType('author_id'));
    }

}
