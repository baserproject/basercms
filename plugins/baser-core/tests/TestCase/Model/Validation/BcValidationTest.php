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

namespace BaserCore\Test\TestCase\Model\Validation;

use BaserCore\Model\Validation\BcValidation;
use BaserCore\TestSuite\BcTestCase;
use Cake\Validation\Validator;

/**
 * Class BcValidationTest
 * @package BaserCore\Test\TestCase\Model\Validation
 * @property BcValidation $BcValidation
 */
class BcValidationTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var BcValidation
     */
    public $BcValidation;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcValidation = new BcValidation();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcValidation);
        parent::tearDown();
    }

    /**
     * Test alphaNumericPlus
     *
     * @return void
     */
    public function testAlphaNumericPlus()
    {
        $alpha = implode('', array_merge(range('a', 'z'), range('A', 'Z')));
        $numeric = implode('', range(0, 9));
        $mark = '-_';
        $allowedChars = $alpha . $numeric . $mark;

        $this->assertEquals(true, $this->BcValidation->alphaNumericPlus(null));
        $this->assertEquals(true, $this->BcValidation->alphaNumericPlus($allowedChars));
        $this->assertEquals(false, $this->BcValidation->alphaNumericPlus($allowedChars . '!'));
        $this->assertEquals(true, $this->BcValidation->alphaNumericPlus($allowedChars . '!', '!'));
        $this->assertEquals(true, $this->BcValidation->alphaNumericPlus($allowedChars . '!', ['!']));
    }

    /**
     * Test confirm
     *
     * @return void
     */
    public function testConfirm()
    {
        $context = [
            'data' => [
                'field0' => true,
                'field1' => true,
                'field2' => false
            ]
        ];

        $this->assertEquals(true, $this->BcValidation->confirm(true, ['field0', 'field1'], $context));
        $this->assertEquals(false, $this->BcValidation->confirm(true, ['field0', 'field2'], $context));
        $this->assertEquals(false, $this->BcValidation->confirm(true, ['field0', 'nofield'], $context));
        $this->assertEquals(true, $this->BcValidation->confirm(true, 'field0', $context));
        $this->assertEquals(true, $this->BcValidation->confirm(true, ['field0'], $context));
        $this->assertEquals(false, $this->BcValidation->confirm(true, 'field2', $context));
        $this->assertEquals(false, $this->BcValidation->confirm(true, ['field2'], $context));
        $this->assertEquals(false, $this->BcValidation->confirm(true, null, $context));
    }

    /**
     * Test notEmptyMultiple
     *
     * @return void
     */
    public function testNotEmptyMultiple()
    {
        $value = [
            '_ids' => [
                'check0' => true,
                'check1' => false
            ],
            'check2' => true,
            'check3' => false
        ];

        $this->assertEquals(false, $this->BcValidation->notEmptyMultiple(['_ids' => true], []));
        $this->assertEquals(false, $this->BcValidation->notEmptyMultiple('value', []));
        $this->assertEquals(true, $this->BcValidation->notEmptyMultiple($value, []));

        unset($value['_ids']['check0']);
        $this->assertEquals(false, $this->BcValidation->notEmptyMultiple($value, []));

        unset($value['_ids']);
        $this->assertEquals(true, $this->BcValidation->notEmptyMultiple($value, []));

        unset($value['check2']);
        $this->assertEquals(false, $this->BcValidation->notEmptyMultiple($value, []));
    }

    /**
     * Test halfText
     *
     * @return void
     */
    public function testHalfText()
    {
        $halfText = 'test';
        $mbText = mb_convert_kana($halfText, 'A');

        $this->assertEquals(true, $this->BcValidation->halfText($halfText));
        $this->assertEquals(false, $this->BcValidation->halfText($mbText));
    }
}
