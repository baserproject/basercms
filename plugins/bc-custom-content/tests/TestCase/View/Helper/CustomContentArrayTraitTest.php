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
        /**
         * case array = 1
         */
        $str = "value1\nvalue2\nvalue3";
        $this->assertEquals(['value1' => 'value1', 'value2' => 'value2', 'value3' => 'value3'],
            $this->textToArray($str));
        /**
         * case array > 1
         */
        $str = "item:item1";
        $this->assertEquals(['item1' => 'item'], $this->textToArray($str));
        /**
         * 改行を含む場合は改行だけで分割し、空白を含む選択肢を扱える
         */
        $str = "B to B\r\nB to C\n\n 東京 本社 \n";
        $this->assertEquals(['B to B' => 'B to B', 'B to C' => 'B to C', '東京 本社' => '東京 本社'],
            $this->textToArray($str));
        // 改行が \r だけの場合も改行で分割する
        $str = "B to B\rB to C";
        $this->assertEquals(['B to B' => 'B to B', 'B to C' => 'B to C'], $this->textToArray($str));
        /**
         * 1 行の場合は従来どおり空白とカンマでも分割する
         */
        $str = "value1,value2 value3";
        $this->assertEquals(['value1' => 'value1', 'value2' => 'value2', 'value3' => 'value3'],
            $this->textToArray($str));
    }
}