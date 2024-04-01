<?php

namespace BcCustomContent\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\View\Helper\CustomContentArrayTrait;

class CustomContentArrayTraitTest extends BcTestCase
{
    use CustomContentArrayTrait;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test arrayValue
     */
    public function test_arrayValue()
    {
        /**
         * array key is numeric
         */
        $array = ['value1'];
        $this->assertEquals('value1', $this->arrayValue(0, $array));
        /**
         * array key exists
         */
        $array = ['key1' => 'value1'];
        $this->assertEquals('value1', $this->arrayValue('key1', $array));
        /**
         * return noValue
         */
        $this->assertEquals('', $this->arrayValue('key2', $array));
        /**
         * array key is array
         */
        $array = [
          [
              'key1' => 'value1'
          ]
        ];
        $this->assertEquals('value1', $this->arrayValue('key1', $array));
    }

    /**
     * test textToArray
     */
    public function test_textToArray()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }
}