<?php

namespace BcBlog\Test\TestCase\Model\Validation;

use BaserCore\TestSuite\BcTestCase;
use BcBlog\Model\Validation\BlogContentValidation;

class BlogContentValidationTest extends BcTestCase
{
    /**
     * アイキャッチ画像サイズバリデーション
     *
     * @dataProvider checkEyeCatchSizeDataProvider
     */
    public function testCheckEyeCatchSize($thumb_width, $thumb_height, $mobile_thumb_width, $mobile_thumb_height, $expected)
    {
        $context['data']['eye_catch_size_thumb_width'] = $thumb_width;
        $context['data']['eye_catch_size_thumb_height'] = $thumb_height;
        $context['data']['eye_catch_size_mobile_thumb_width'] = $mobile_thumb_width;
        $context['data']['eye_catch_size_mobile_thumb_height'] = $mobile_thumb_height;
        $rs = BlogContentValidation::checkEyeCatchSize("test", $context);
        $this->assertEquals($rs, $expected);
    }

    public function checkEyeCatchSizeDataProvider()
    {
        return [
            [600, 600, 100, 100, true],
            ['', 600, 100, 100, false],
            [600, '', 100, 100, false],
            [600, 600, '', 100, false],
            [600, 600, 100, '', false],
        ];
    }

}
